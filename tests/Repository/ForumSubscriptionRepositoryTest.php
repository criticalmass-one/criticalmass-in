<?php declare(strict_types=1);

namespace Tests\Repository;

use App\Entity\Board;
use App\Entity\ForumSubscription;
use App\Entity\Thread;
use App\Entity\User;
use App\Repository\ForumSubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Führt die Abfragen wirklich aus, statt das Repository zu mocken.
 *
 * Der Grund steht in der Geschichte dieses Repositories: Ein „SELECT DISTINCT u“ mit
 * u als blossem Join-Alias ist ungültiges DQL, und ein gemocktes Repository hat das
 * anstandslos durchgehen lassen — bis jede Antwort im Forum mit einem 500er endete.
 */
class ForumSubscriptionRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private ForumSubscriptionRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->entityManager->getRepository(ForumSubscription::class);

        // Die Tests teilen sich eine Datenbank ohne Rollback; ohne diesen Schnitt
        // findet ein Test die Abos des vorherigen.
        $this->entityManager->createQuery('DELETE FROM ' . ForumSubscription::class . ' s')->execute();
    }

    private function user(string $email): User
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        self::assertInstanceOf(User::class, $user);

        return $user;
    }

    private function thread(): Thread
    {
        $board = $this->entityManager->getRepository(Board::class)->findOneBy(['slug' => 'general']);
        self::assertInstanceOf(Board::class, $board);

        $thread = new Thread();
        $thread->setBoard($board)->setTitle('Abo-Testthema')->setSlug('abo-testthema-' . uniqid());

        $this->entityManager->persist($thread);
        $this->entityManager->flush();

        return $thread;
    }

    private function subscribe(User $user, ?Thread $thread, ?Board $board, bool $globalScope): void
    {
        $subscription = (new ForumSubscription())
            ->setUser($user)
            ->setThread($thread)
            ->setBoard($board)
            ->setGlobalScope($globalScope);

        $this->entityManager->persist($subscription);
        $this->entityManager->flush();
    }

    public function testThreadSubscriberIsFound(): void
    {
        $thread = $this->thread();
        $user = $this->user('testuser@criticalmass.in');

        $this->subscribe($user, $thread, null, false);

        self::assertContains($user, $this->repository->findSubscribersForThread($thread));
    }

    public function testBoardSubscriberIsFoundForAThreadInThatBoard(): void
    {
        $thread = $this->thread();
        $user = $this->user('cyclist@criticalmass.in');

        $this->subscribe($user, null, $thread->getBoard(), false);

        self::assertContains($user, $this->repository->findSubscribersForThread($thread));
    }

    public function testGlobalSubscriberIsFoundEverywhere(): void
    {
        $thread = $this->thread();
        $user = $this->user('admin@criticalmass.in');

        $this->subscribe($user, null, null, true);

        self::assertContains($user, $this->repository->findSubscribersForThread($thread));
    }

    public function testSomeoneSubscribedTwiceAppearsOnce(): void
    {
        $thread = $this->thread();
        $user = $this->user('testuser@criticalmass.in');

        $this->subscribe($user, $thread, null, false);
        $this->subscribe($user, null, $thread->getBoard(), false);

        $subscribers = $this->repository->findSubscribersForThread($thread);
        $matches = array_filter($subscribers, static fn (User $candidate): bool => $candidate === $user);

        self::assertCount(1, $matches, 'Mehrere Abonnements derselben Person ergeben einen Empfänger.');
    }

    public function testUnrelatedSubscriptionsStayOut(): void
    {
        $thread = $this->thread();
        $other = $this->thread();
        $user = $this->user('photodownloader@criticalmass.in');

        $this->subscribe($user, $other, null, false);

        self::assertNotContains($user, $this->repository->findSubscribersForThread($thread));
    }

    public function testFindExistingDistinguishesTheLevels(): void
    {
        $thread = $this->thread();
        $user = $this->user('cyclist@criticalmass.in');

        $this->subscribe($user, $thread, null, false);

        self::assertNotNull($this->repository->findExisting($user, $thread, null, null, false));
        self::assertNull(
            $this->repository->findExisting($user, null, $thread->getBoard(), null, false),
            'Ein Thema-Abo ist kein Forum-Abo.'
        );
        self::assertNull($this->repository->findExisting($user, null, null, null, true));
    }
}
