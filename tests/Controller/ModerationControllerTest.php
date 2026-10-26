<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\Board;
use App\Entity\Post;
use App\Entity\PostReport;
use App\Entity\Ride;
use App\Entity\Thread;
use App\Enum\PostReportReasonEnum;
use App\Enum\PostReportStatusEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Mime\Email;

/**
 * Entscheidungen ueber gemeldete Beitraege.
 */
class ModerationControllerTest extends AbstractControllerTestCase
{
    private const string AUTHOR = 'testuser@criticalmass.in';
    private const string REPORTER = 'cyclist@criticalmass.in';
    private const string ADMIN = 'admin@criticalmass.in';

    private function em(): EntityManagerInterface
    {
        return static::getContainer()->get('doctrine')->getManager();
    }

    private function createReportedPost(?Thread $thread = null): Post
    {
        $em = $this->em();

        $post = (new Post())
            ->setUser($this->getUser(self::AUTHOR))
            ->setText('Fragwuerdiger Beitrag ' . uniqid());

        if (null !== $thread) {
            $post->setThread($thread);
        } else {
            $post->setRide($em->getRepository(Ride::class)->findOneBy([]));
        }

        $report = (new PostReport($post))
            ->setReporter($this->getUser(self::REPORTER))
            ->setReason(PostReportReasonEnum::HARASSMENT);

        $em->persist($post);
        $em->persist($report);
        $em->flush();

        return $post;
    }

    private function token(KernelBrowser $client, Post $post, string $action): string
    {
        $crawler = $client->request('GET', '/moderation/reports');

        return (string) $crawler
            ->filter(sprintf('form[action="/moderation/post/%d/%s"] input[name="_token"]', $post->getId(), $action))
            ->attr('value');
    }

    private function reload(Post $post): Post
    {
        $this->em()->clear();

        return $this->em()->getRepository(Post::class)->find($post->getId());
    }

    /**
     * @return list<PostReport>
     */
    private function reportsFor(Post $post): array
    {
        return $this->em()->getRepository(PostReport::class)->findBy(['post' => $post->getId()]);
    }

    public function testOnlyAdminsSeeTheReports(): void
    {
        $client = static::createClient();
        $this->loginAs($client, self::REPORTER);

        $client->request('GET', '/moderation/reports');

        self::assertResponseStatusCodeSame(403);
    }

    public function testAdminsSeeOpenReports(): void
    {
        $client = static::createClient();
        $post = $this->createReportedPost();
        $this->loginAs($client, self::ADMIN);

        $crawler = $client->request('GET', '/moderation/reports');

        self::assertResponseIsSuccessful();
        self::assertStringContainsString((string) $post->getText(), $crawler->filter(sprintf('#report-post-%d', $post->getId()))->text());
        self::assertStringContainsString('Beleidigung oder Belästigung', $crawler->filter(sprintf('#report-post-%d', $post->getId()))->text());
    }

    public function testRemovingNeedsAReason(): void
    {
        $client = static::createClient();
        $post = $this->createReportedPost();
        $this->loginAs($client, self::ADMIN);

        $client->request('POST', sprintf('/moderation/post/%d/remove', $post->getId()), [
            '_token' => $this->token($client, $post, 'remove'),
            'reason' => '   ',
        ]);

        self::assertResponseRedirects('/moderation/reports');
        self::assertTrue($this->reload($post)->getEnabled());
        self::assertTrue($this->reportsFor($post)[0]->isOpen());
    }

    public function testRemovingHidesThePostAndInformsEveryone(): void
    {
        $client = static::createClient();
        $post = $this->createReportedPost();
        $this->loginAs($client, self::ADMIN);

        $client->request('POST', sprintf('/moderation/post/%d/remove', $post->getId()), [
            '_token' => $this->token($client, $post, 'remove'),
            'reason' => 'Persönliche Beleidigung eines anderen Teilnehmers.',
        ]);

        self::assertResponseRedirects('/moderation/reports');
        self::assertFalse($this->reload($post)->getEnabled());

        $report = $this->reportsFor($post)[0];
        self::assertSame(PostReportStatusEnum::REMOVED, $report->getStatus());
        self::assertSame('Persönliche Beleidigung eines anderen Teilnehmers.', $report->getDecisionNote());
        self::assertSame(self::ADMIN, $report->getDecidedBy()?->getEmail());

        // Die Entscheidung an den Meldenden, die Begruendung an den Autor.
        self::assertEmailCount(2);
        $mails = [];
        foreach ($this->getMailerMessages() as $mail) {
            self::assertInstanceOf(Email::class, $mail);
            $mails[$mail->getTo()[0]->getAddress()] = $mail;
        }
        self::assertArrayHasKey(self::REPORTER, $mails);
        self::assertArrayHasKey(self::AUTHOR, $mails);
        self::assertStringContainsString('Persönliche Beleidigung eines anderen Teilnehmers.', (string) $mails[self::AUTHOR]->getHtmlBody());
    }

    public function testDismissingLeavesThePost(): void
    {
        $client = static::createClient();
        $post = $this->createReportedPost();
        $this->loginAs($client, self::ADMIN);

        $client->request('POST', sprintf('/moderation/post/%d/dismiss', $post->getId()), [
            '_token' => $this->token($client, $post, 'dismiss'),
        ]);

        self::assertResponseRedirects('/moderation/reports');
        self::assertTrue($this->reload($post)->getEnabled());
        self::assertSame(PostReportStatusEnum::DISMISSED, $this->reportsFor($post)[0]->getStatus());

        // Nur der Meldende erfaehrt davon, der Autor nicht.
        self::assertEmailCount(1);
        $mail = $this->getMailerMessages()[0];
        self::assertInstanceOf(Email::class, $mail);
        self::assertSame(self::REPORTER, $mail->getTo()[0]->getAddress());
    }

    public function testWithoutTokenNothingHappens(): void
    {
        $client = static::createClient();
        $post = $this->createReportedPost();
        $this->loginAs($client, self::ADMIN);

        $client->request('POST', sprintf('/moderation/post/%d/remove', $post->getId()), ['reason' => 'egal']);

        self::assertResponseStatusCodeSame(403);
        self::assertTrue($this->reload($post)->getEnabled());
    }

    public function testRemovingAThreadOpenerRemovesTheThread(): void
    {
        $client = static::createClient();
        $em = $this->em();

        $thread = (new Thread())
            ->setTitle('Gemeldetes Thema')
            ->setSlug('gemeldetes-thema-' . uniqid())
            ->setBoard($em->getRepository(Board::class)->findOneBy([]));
        $em->persist($thread);

        $post = $this->createReportedPost($thread);
        $thread->setFirstPost($post)->setLastPost($post);
        $em->flush();

        $this->loginAs($client, self::ADMIN);

        $client->request('POST', sprintf('/moderation/post/%d/remove', $post->getId()), [
            '_token' => $this->token($client, $post, 'remove'),
            'reason' => 'Spam.',
        ]);

        $em->clear();
        self::assertFalse($em->getRepository(Thread::class)->find($thread->getId())->getEnabled());
    }
}
