<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Criticalmass\FriendlyCaptcha\FriendlyCaptchaInterface;
use App\Criticalmass\Router\PostUrlGenerator;
use App\Entity\Post;
use App\Entity\PostReport;
use App\Entity\Ride;
use App\Entity\User;
use App\Enum\PostReportReasonEnum;
use App\Enum\PostReportStatusEnum;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DomCrawler\Field\ChoiceFormField;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Email;

/**
 * Beitraege melden — mit Konto und als Gast.
 */
class PostReportControllerTest extends AbstractControllerTestCase
{
    private const string AUTHOR = 'testuser@criticalmass.in';
    private const string REPORTER = 'cyclist@criticalmass.in';

    /**
     * Ein frischer Kommentar zu einer Hamburger Tour, damit kein Test die
     * Meldungen eines anderen sieht.
     */
    private function createPost(): Post
    {
        $em = static::getContainer()->get('doctrine')->getManager();

        $ride = $em->getRepository(Ride::class)
            ->createQueryBuilder('r')
            ->join('r.city', 'c')
            ->join('c.mainSlug', 'cs')
            ->where('cs.slug = :slug')
            ->setParameter('slug', 'hamburg')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();

        $post = (new Post())
            ->setRide($ride)
            ->setUser($this->getUser(self::AUTHOR))
            ->setText('Gemeldeter Testbeitrag ' . uniqid());

        $em->persist($post);
        $em->flush();

        return $post;
    }

    private function resetLimiter(string $key): void
    {
        static::getContainer()->get('limiter.post_report')->create($key)->reset();
    }

    /**
     * Ersetzt das Captcha, das sonst die Friendly-Captcha-API fragen wuerde.
     * Ohne disableReboot() baute der naechste Request den Container neu.
     */
    private function stubCaptcha(KernelBrowser $client, bool $solved): void
    {
        $client->disableReboot();

        static::getContainer()->set(FriendlyCaptchaInterface::class, new class($solved) implements FriendlyCaptchaInterface {
            public function __construct(private readonly bool $solved)
            {
            }

            public function checkCaptcha(Request $request): bool
            {
                return $this->solved;
            }
        });
    }

    /**
     * @return list<PostReport>
     */
    private function reportsFor(Post $post): array
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        $em->clear();

        return $em->getRepository(PostReport::class)->findBy(['post' => $post->getId()]);
    }

    private function submitReport(KernelBrowser $client, Post $post, string $reason, string $explanation = '', ?string $email = null): void
    {
        $crawler = $client->request('GET', sprintf('/post/%d/report', $post->getId()));
        self::assertResponseIsSuccessful();

        $form = $crawler->filter('#form-post-report')->form();
        $form['post_report[reason]'] = $reason;
        $form['post_report[explanation]'] = $explanation;
        /** @var ChoiceFormField $goodFaith */
        $goodFaith = $form['post_report[goodFaith]'];
        $goodFaith->tick();

        if (null !== $email) {
            $form['post_report[reporterEmail]'] = $email;
        }

        $client->submit($form);
    }

    public function testTheReportLinkShowsForOthersButNotForTheAuthor(): void
    {
        $client = static::createClient();
        $post = $this->createPost();
        $reportUrl = sprintf('/post/%d/report', $post->getId());
        $pageUrl = strtok(static::getContainer()->get(PostUrlGenerator::class)->generate($post), '#');

        $crawler = $client->request('GET', $pageUrl);
        self::assertCount(1, $crawler->filter(sprintf('a[href="%s"]', $reportUrl)), 'Gaeste koennen melden');

        $this->loginAs($client, self::REPORTER);
        $crawler = $client->request('GET', $pageUrl);
        self::assertCount(1, $crawler->filter(sprintf('a[href="%s"]', $reportUrl)));

        $this->loginAs($client, self::AUTHOR);
        $crawler = $client->request('GET', $pageUrl);
        self::assertCount(0, $crawler->filter(sprintf('a[href="%s"]', $reportUrl)));
    }

    public function testAUserReportsAPost(): void
    {
        $client = static::createClient();
        $post = $this->createPost();
        $reporter = $this->getUser(self::REPORTER);
        $this->resetLimiter('user-' . $reporter->getId());
        $this->loginAs($client, self::REPORTER);

        $this->submitReport($client, $post, 'SPAM');

        self::assertResponseRedirects();
        self::assertStringContainsString(sprintf('#post-%d', $post->getId()), (string) $client->getResponse()->headers->get('Location'));

        // Eine Mail an den Admin der Fixtures, eine Eingangsbestaetigung an den Meldenden.
        self::assertEmailCount(2);
        $recipients = [];
        foreach ($this->getMailerMessages() as $mail) {
            self::assertInstanceOf(Email::class, $mail);
            $recipients[] = $mail->getTo()[0]->getAddress();
        }
        self::assertContains('admin@criticalmass.in', $recipients);
        self::assertContains(self::REPORTER, $recipients);

        $reports = $this->reportsFor($post);
        self::assertCount(1, $reports);
        self::assertSame(PostReportReasonEnum::SPAM, $reports[0]->getReason());
        self::assertSame(PostReportStatusEnum::OPEN, $reports[0]->getStatus());
        self::assertSame($reporter->getId(), $reports[0]->getReporter()?->getId());
    }

    public function testIllegalContentNeedsAnExplanation(): void
    {
        $client = static::createClient();
        $post = $this->createPost();
        $this->loginAs($client, self::REPORTER);

        $this->submitReport($client, $post, 'ILLEGAL');

        self::assertResponseStatusCodeSame(422);
        self::assertSelectorTextContains('body', 'Bitte erkläre kurz');
        self::assertCount(0, $this->reportsFor($post));
    }

    public function testTheGoodFaithStatementIsRequired(): void
    {
        $client = static::createClient();
        $post = $this->createPost();
        $this->loginAs($client, self::REPORTER);

        $crawler = $client->request('GET', sprintf('/post/%d/report', $post->getId()));
        $form = $crawler->filter('#form-post-report')->form();
        $form['post_report[reason]'] = 'SPAM';
        $client->submit($form);

        self::assertResponseStatusCodeSame(422);
        self::assertCount(0, $this->reportsFor($post));
    }

    public function testAuthorsCannotReportTheirOwnPost(): void
    {
        $client = static::createClient();
        $post = $this->createPost();
        $this->loginAs($client, self::AUTHOR);

        $client->request('GET', sprintf('/post/%d/report', $post->getId()));

        self::assertResponseRedirects();
        self::assertCount(0, $this->reportsFor($post));
    }

    public function testASecondReportBySamePersonIsNotStored(): void
    {
        $client = static::createClient();
        $post = $this->createPost();
        $this->resetLimiter('user-' . $this->getUser(self::REPORTER)->getId());
        $this->loginAs($client, self::REPORTER);

        $this->submitReport($client, $post, 'SPAM');
        $client->request('GET', sprintf('/post/%d/report', $post->getId()));

        self::assertResponseRedirects();
        self::assertCount(1, $this->reportsFor($post));
    }

    public function testWithdrawnPostsCannotBeReported(): void
    {
        $client = static::createClient();
        $post = $this->createPost();
        $post->setEnabled(false);
        static::getContainer()->get('doctrine')->getManager()->flush();

        $client->request('GET', sprintf('/post/%d/report', $post->getId()));

        self::assertResponseStatusCodeSame(404);
    }

    public function testGuestsReportWithAnAddressAndACaptcha(): void
    {
        $client = static::createClient();
        $this->stubCaptcha($client, true);
        $this->resetLimiter('ip-127.0.0.1');
        $post = $this->createPost();

        $this->submitReport($client, $post, 'HARASSMENT', '', 'gast@example.org');

        self::assertResponseRedirects();

        $reports = $this->reportsFor($post);
        self::assertCount(1, $reports);
        self::assertNull($reports[0]->getReporter());
        self::assertSame('gast@example.org', $reports[0]->getContactEmail());
    }

    public function testGuestsWithoutASolvedCaptchaAreTurnedAway(): void
    {
        $client = static::createClient();
        $this->stubCaptcha($client, false);
        $post = $this->createPost();

        $this->submitReport($client, $post, 'SPAM', '', 'gast@example.org');

        self::assertResponseIsSuccessful();
        self::assertCount(0, $this->reportsFor($post));
    }

    public function testGuestsMustLeaveAnAddress(): void
    {
        $client = static::createClient();
        $this->stubCaptcha($client, true);
        $post = $this->createPost();

        $this->submitReport($client, $post, 'SPAM', '', '');

        self::assertResponseStatusCodeSame(422);
        self::assertCount(0, $this->reportsFor($post));
    }

    public function testTheEleventhReportWithinAnHourIsRejected(): void
    {
        $client = static::createClient();
        $reporter = $this->getUser(self::REPORTER);
        $this->resetLimiter('user-' . $reporter->getId());
        $this->loginAs($client, self::REPORTER);

        for ($i = 1; $i <= 10; ++$i) {
            $post = $this->createPost();
            $this->submitReport($client, $post, 'SPAM');
            self::assertCount(1, $this->reportsFor($post), sprintf('Meldung %d sollte durchgehen', $i));
        }

        $post = $this->createPost();
        $this->submitReport($client, $post, 'SPAM');

        self::assertCount(0, $this->reportsFor($post));
    }
}
