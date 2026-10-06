<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\City;
use App\Entity\Post;
use App\Entity\User;
use App\Enum\PostKindEnum;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

/**
 * Statusbeitraege auf der Stadtseite.
 *
 * Der Schalter `status_posts` liest FEATURE_STATUS_POSTS bei jeder Abfrage aus
 * $_ENV; die Tests legen ihn deshalb pro Fall um.
 */
class CityStatusPostControllerTest extends AbstractControllerTestCase
{
    private const string USER = 'cyclist@criticalmass.in';

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

    private function getCity(): City
    {
        $em = static::getContainer()->get('doctrine')->getManager();

        return $em->getRepository(City::class)
            ->createQueryBuilder('c')
            ->join('c.mainSlug', 'cs')
            ->where('cs.slug = :slug')
            ->setParameter('slug', 'hamburg')
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * Der Ratenbegrenzer liegt im Cache und ueberdauert einzelne Tests.
     */
    private function resetLimiter(User $user): void
    {
        static::getContainer()->get('limiter.status_post')->create((string) $user->getId())->reset();
    }

    private function writeStatusPost(KernelBrowser $client, string $text): void
    {
        $crawler = $client->request('GET', '/hamburg');

        $form = $crawler->filter('#form-post')->form();
        $form['post[text]'] = $text;

        $client->submit($form);
    }

    private function findPost(string $text): ?Post
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        $em->clear();

        return $em->getRepository(Post::class)->findOneBy(['text' => $text]);
    }

    public function testSwitchedOffTheCityPageShowsNoStatusPosts(): void
    {
        $_ENV['FEATURE_STATUS_POSTS'] = 'false';

        $client = static::createClient();
        $this->loginAs($client, self::USER);

        $crawler = $client->request('GET', '/hamburg');

        self::assertResponseIsSuccessful();
        self::assertCount(0, $crawler->filter('#status-posts'));
        self::assertCount(0, $crawler->filter('#form-post'));
    }

    public function testSwitchedOffWritingIsNotPossible(): void
    {
        $_ENV['FEATURE_STATUS_POSTS'] = 'false';

        $client = static::createClient();
        $this->loginAs($client, self::USER);

        $client->request('GET', sprintf('/post/write/city/%d', $this->getCity()->getId()));

        self::assertResponseStatusCodeSame(404);
    }

    public function testGuestsSeeThePostsButNoForm(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/hamburg');

        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('#status-posts'));
        self::assertCount(0, $crawler->filter('#form-post'));
        self::assertCount(1, $crawler->filter('#status-posts [data-controller="hint-modal"]'));
    }

    public function testAStatusPostLandsOnTheCityPage(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::USER);
        $this->resetLimiter($this->getUser(self::USER));

        $text = 'Wer kommt am Freitag aus Altona mit? ' . uniqid();
        $this->writeStatusPost($client, $text);

        $post = $this->findPost($text);

        self::assertNotNull($post);
        self::assertSame(PostKindEnum::STATUS, $post->getKind());
        self::assertSame('hamburg', $post->getCity()?->getMainSlugString());
        self::assertNull($post->getRide());
        self::assertResponseRedirects(sprintf('/hamburg#post-%d', $post->getId()));

        $crawler = $client->request('GET', '/hamburg');

        self::assertStringContainsString(
            $text,
            $crawler->filter(sprintf('#status-posts #post-%d', $post->getId()))->text()
        );
    }

    public function testWithdrawnPostsDisappear(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::USER);
        $this->resetLimiter($this->getUser(self::USER));

        $text = 'Gleich wieder weg ' . uniqid();
        $this->writeStatusPost($client, $text);

        $post = $this->findPost($text);
        self::assertNotNull($post);

        $em = static::getContainer()->get('doctrine')->getManager();
        $post->setEnabled(false);
        $em->persist($post);
        $em->flush();

        $crawler = $client->request('GET', '/hamburg');

        self::assertCount(0, $crawler->filter(sprintf('#post-%d', $post->getId())));
    }

    public function testCommentsOnTheCityAreNoStatusPosts(): void
    {
        $client = static::createClient();
        $em = static::getContainer()->get('doctrine')->getManager();

        $comment = (new Post())
            ->setCity($this->getCity())
            ->setUser($this->getUser(self::USER))
            ->setText('Alter Kommentar zur Stadt');

        $em->persist($comment);
        $em->flush();

        $crawler = $client->request('GET', '/hamburg');

        self::assertCount(0, $crawler->filter(sprintf('#post-%d', $comment->getId())));
    }

    public function testTheSixthPostWithinAnHourIsRejected(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::USER);
        $this->resetLimiter($this->getUser(self::USER));

        $run = uniqid();

        for ($i = 1; $i <= 5; ++$i) {
            $this->writeStatusPost($client, sprintf('Ratenbegrenzung %s/%d', $run, $i));
            self::assertNotNull($this->findPost(sprintf('Ratenbegrenzung %s/%d', $run, $i)));
        }

        $this->writeStatusPost($client, sprintf('Ratenbegrenzung %s/6', $run));

        self::assertNull($this->findPost(sprintf('Ratenbegrenzung %s/6', $run)));
        self::assertResponseRedirects('/hamburg');

        $crawler = $client->followRedirect();
        self::assertStringContainsString('in der letzten Stunde', $crawler->filter('.alert-danger')->text());
    }
}
