<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Controller\StatusPostController;
use App\Criticalmass\Timeline\Collector\StatusPostCollector;
use App\Criticalmass\Timeline\Item\StatusPostItem;
use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\Post;
use App\Entity\Ride;
use App\Enum\PostKindEnum;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Statusbeitraege aus allen Staedten, die einer Person und in der Timeline.
 */
class StatusFeedControllerTest extends AbstractControllerTestCase
{
    private const string AUTHOR = 'cyclist@criticalmass.in';
    private const string OTHER = 'testuser@criticalmass.in';

    protected function setUp(): void
    {
        parent::setUp();

        $_ENV['FEATURE_STATUS_POSTS'] = 'true';
    }

    protected function tearDown(): void
    {
        unset($_ENV['FEATURE_STATUS_POSTS']);

        parent::tearDown();
    }

    private function em(): EntityManagerInterface
    {
        return static::getContainer()->get('doctrine')->getManager();
    }

    private function hamburg(): City
    {
        return $this->em()->getRepository(City::class)
            ->createQueryBuilder('c')
            ->join('c.mainSlug', 'cs')
            ->where('cs.slug = :slug')
            ->setParameter('slug', 'hamburg')
            ->getQuery()
            ->getSingleResult();
    }

    private function createPost(string $text, string $email = self::AUTHOR, ?City $city = null, PostKindEnum $kind = PostKindEnum::STATUS, bool $enabled = true): Post
    {
        $post = (new Post())
            ->setCity($city ?? $this->hamburg())
            ->setUser($this->getUser($email))
            ->setKind($kind)
            ->setEnabled($enabled)
            ->setText($text);

        $this->em()->persist($post);
        $this->em()->flush();

        return $post;
    }

    public function testSwitchedOffBothPagesAreMissing(): void
    {
        $_ENV['FEATURE_STATUS_POSTS'] = 'false';

        $client = static::createClient();

        $client->request('GET', '/beitraege');
        self::assertResponseStatusCodeSame(404);

        $client->request('GET', '/beitraege/cyclist');
        self::assertResponseStatusCodeSame(404);

        $crawler = $client->request('GET', '/hamburg');
        self::assertCount(0, $crawler->filter('a[href="/beitraege"]'));
    }

    public function testTheMenuLinksTheFeed(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/hamburg');

        self::assertGreaterThan(0, $crawler->filter('a[href="/beitraege"]')->count());
    }

    public function testTheFeedShowsStatusPostsOnly(): void
    {
        $client = static::createClient();
        $run = uniqid();

        $status = $this->createPost('Status ' . $run);
        $this->createPost('Kommentar ' . $run, kind: PostKindEnum::COMMENT);
        $this->createPost('Zurueckgezogen ' . $run, enabled: false);

        $crawler = $client->request('GET', '/beitraege');

        self::assertResponseIsSuccessful();
        $text = $crawler->filter('.post-list')->text();
        self::assertStringContainsString('Status ' . $run, $text);
        self::assertStringNotContainsString('Kommentar ' . $run, $text);
        self::assertStringNotContainsString('Zurueckgezogen ' . $run, $text);

        // Ausserhalb der Stadtseite steht dabei, woher der Beitrag kommt.
        self::assertStringContainsString('über', $crawler->filter(sprintf('#post-%d', $status->getId()))->text());
    }

    public function testPostsFromDisabledCitiesStayHidden(): void
    {
        $client = static::createClient();
        $em = $this->em();
        $hamburg = $this->hamburg();

        $slug = (new CitySlug())->setSlug('abgeschaltet-' . uniqid());
        // Einige Setter von City sind auf Interfaces getypt; deshalb keine Kette.
        $city = new City();
        $city->setCity('Abgeschaltet');
        $city->setTitle('Critical Mass Abgeschaltet');
        $city->setLatitude(53.5);
        $city->setLongitude(10.0);
        $city->setRegion($hamburg->getRegion());
        $city->setTimezone('Europe/Berlin');
        $city->setEnabled(false);
        $city->addSlug($slug);
        $slug->setCity($city);
        $em->persist($slug);
        $em->persist($city);
        $em->flush();

        $text = 'Aus einer abgeschalteten Stadt ' . uniqid();
        $this->createPost($text, city: $city);

        $crawler = $client->request('GET', '/beitraege');

        self::assertStringNotContainsString($text, $crawler->filter('body')->text());
    }

    public function testTheUserPageShowsOnlyThatPersonsPosts(): void
    {
        $client = static::createClient();
        $run = uniqid();

        $this->createPost('Von cyclist ' . $run);
        $this->createPost('Von testuser ' . $run, self::OTHER);

        $crawler = $client->request('GET', '/beitraege/cyclist');

        self::assertResponseIsSuccessful();
        $text = $crawler->filter('.post-list')->text();
        self::assertStringContainsString('Von cyclist ' . $run, $text);
        self::assertStringNotContainsString('Von testuser ' . $run, $text);
    }

    public function testAuthorNamesOfStatusPostsLeadToTheirPosts(): void
    {
        $client = static::createClient();
        $post = $this->createPost('Mit Namen ' . uniqid());

        $crawler = $client->request('GET', '/beitraege');

        self::assertCount(1, $crawler->filter(sprintf('#post-%d a[href="/beitraege/cyclist"]', $post->getId())));
    }

    public function testTheFeedIsPaginated(): void
    {
        $client = static::createClient();
        $run = uniqid();

        for ($i = 1; $i <= StatusPostController::POSTS_PER_PAGE + 1; ++$i) {
            $this->createPost(sprintf('Seite %s/%02d', $run, $i));
        }

        $crawler = $client->request('GET', '/beitraege');
        self::assertCount(StatusPostController::POSTS_PER_PAGE, $crawler->filter('.post-list .forum-post'));
        self::assertGreaterThan(0, $crawler->filter('a[href*="page=2"]')->count());

        $crawler = $client->request('GET', '/beitraege?page=2');
        self::assertResponseIsSuccessful();
        self::assertGreaterThan(0, $crawler->filter('.post-list .forum-post')->count());
    }

    public function testTheTimelinePicksUpStatusPosts(): void
    {
        static::createClient();
        $post = $this->createPost('Fuer die Timeline ' . uniqid());

        $collector = static::getContainer()->get(StatusPostCollector::class);
        $collector->setDateRange(new \DateTime('-1 day'), new \DateTime('+1 day'))->execute();

        $items = array_filter(
            $collector->getItems(),
            fn($item) => $item instanceof StatusPostItem && $item->getPost()->getId() === $post->getId()
        );
        self::assertCount(1, $items);

        $html = static::getContainer()->get('twig')->render('Timeline/Items/statusPost.html.twig', ['item' => array_values($items)[0]]);
        self::assertStringContainsString((string) $post->getText(), $html);
        self::assertStringContainsString(sprintf('#post-%d', $post->getId()), $html);
    }

    public function testRideCommentsAreNoStatusPosts(): void
    {
        static::createClient();
        $ride = $this->em()->getRepository(Ride::class)->findOneBy([]);

        $post = (new Post())
            ->setRide($ride)
            ->setUser($this->getUser(self::AUTHOR))
            ->setText('Tourkommentar ' . uniqid());
        $this->em()->persist($post);
        $this->em()->flush();

        $collector = static::getContainer()->get(StatusPostCollector::class);
        $collector->setDateRange(new \DateTime('-1 day'), new \DateTime('+1 day'))->execute();

        foreach ($collector->getItems() as $item) {
            self::assertNotSame($post->getId(), $item->getPost()->getId());
        }
    }
}
