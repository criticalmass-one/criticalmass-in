<?php declare(strict_types=1);

namespace Tests\Criticalmass\Forum;

use App\Criticalmass\Forum\ForumNotifier;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\Post;
use App\Entity\Thread;
use App\Entity\User;
use App\Repository\ForumSubscriptionRepository;
use App\Repository\PostRepository;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ForumNotifierTest extends TestCase
{
    private int $nextUserId = 1;

    private function user(string $email, bool $wantsMails = true): User
    {
        $user = new User();
        $user->setEmail($email)->setForumNotifications($wantsMails);

        // Ohne Id greift die Entdopplung nicht — Doctrine vergibt sie sonst.
        $reflection = new \ReflectionProperty(User::class, 'id');
        $reflection->setValue($user, $this->nextUserId++);

        return $user;
    }

    /**
     * @param list<User> $subscribers
     */
    private function notifier(array $subscribers): ForumNotifier
    {
        $subscriptionRepository = $this->createMock(ForumSubscriptionRepository::class);
        $subscriptionRepository->method('findSubscribersForThread')->willReturn($subscribers);

        return new ForumNotifier(
            $subscriptionRepository,
            $this->createMock(PostRepository::class),
            $this->createMock(NotifierInterface::class),
            $this->createMock(ObjectRouterInterface::class),
            $this->createMock(UrlGeneratorInterface::class),
            new NullLogger(),
            'noreply@criticalmass.in'
        );
    }

    private function postIn(Thread $thread, ?User $author): Post
    {
        $post = new Post();
        $post->setThread($thread);

        if (null !== $author) {
            $post->setUser($author);
        }

        return $post;
    }

    public function testSubscribersAreNotified(): void
    {
        $thread = new Thread();
        $reader = $this->user('leser@example.org');

        $recipients = $this->notifier([$reader])->recipients($this->postIn($thread, $this->user('autor@example.org')));

        self::assertSame([$reader], $recipients);
    }

    public function testTheAuthorDoesNotGetAMailAboutTheirOwnPost(): void
    {
        $author = $this->user('autor@example.org');
        $thread = new Thread();

        $recipients = $this->notifier([$author])->recipients($this->postIn($thread, $author));

        self::assertSame([], $recipients);
    }

    public function testEveryoneIsNotifiedOnlyOnce(): void
    {
        $reader = $this->user('leser@example.org');

        // Dasselbe Konto ueber Thema und Forum zugleich abonniert.
        $recipients = $this->notifier([$reader, $reader])
            ->recipients($this->postIn(new Thread(), $this->user('autor@example.org')));

        self::assertCount(1, $recipients);
    }

    public function testSwitchedOffNotificationsAreRespected(): void
    {
        $reader = $this->user('stillleser@example.org', false);

        $recipients = $this->notifier([$reader])
            ->recipients($this->postIn(new Thread(), $this->user('autor@example.org')));

        self::assertSame([], $recipients, 'Der Hauptschalter im Konto schlaegt das Abonnement.');
    }

    public function testAccountsWithoutMailAddressAreSkipped(): void
    {
        $reader = new User();
        $reader->setForumNotifications(true);

        $recipients = $this->notifier([$reader])
            ->recipients($this->postIn(new Thread(), $this->user('autor@example.org')));

        self::assertSame([], $recipients);
    }

    public function testCommentsOutsideAThreadNotifyNobody(): void
    {
        $post = new Post();

        // Kommentare an Touren, Staedten und Fotos gehoeren zu keinem Thema.
        self::assertSame([], $this->notifier([$this->user('leser@example.org')])->recipients($post));
    }
}
