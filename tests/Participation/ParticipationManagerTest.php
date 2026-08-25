<?php declare(strict_types=1);

namespace Tests\Participation;

use App\Criticalmass\Participation\Manager\ParticipationManager;
use App\Entity\Participation;
use App\Entity\Ride;
use App\Entity\User;
use App\Event\Participation\ParticipationCreatedEvent;
use App\Repository\ParticipationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class ParticipationManagerTest extends TestCase
{
    private User $user;
    private Ride $ride;
    private MockObject&ParticipationRepository $repository;
    private MockObject&ObjectManager $manager;
    private MockObject&EventDispatcherInterface $dispatcher;
    private ParticipationManager $participationManager;

    protected function setUp(): void
    {
        $this->user = new User();
        $this->ride = new Ride();

        $this->repository = $this->createMock(ParticipationRepository::class);
        $this->manager = $this->createMock(ObjectManager::class);
        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getRepository')->with(Participation::class)->willReturn($this->repository);
        $registry->method('getManager')->willReturn($this->manager);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($this->user);
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')->willReturn($token);

        $this->participationManager = new ParticipationManager($registry, $tokenStorage, $this->dispatcher);
    }

    /**
     * @return iterable<string, array{0: string, 1: bool, 2: bool, 3: bool}>
     */
    public static function statuses(): iterable
    {
        yield 'yes' => ['yes', true, false, false];
        yield 'maybe' => ['maybe', false, true, false];
        yield 'no' => ['no', false, false, true];
        yield 'unknown status clears everything' => ['perhaps', false, false, false];
    }

    #[Test]
    #[DataProvider('statuses')]
    public function createsParticipationForCurrentUserWithGivenStatus(string $status, bool $yes, bool $maybe, bool $no): void
    {
        $this->repository->method('findParticipationForUserAndRide')->with($this->user, $this->ride)->willReturn(null);
        $this->manager->expects(self::once())->method('persist')->with(self::isInstanceOf(Participation::class));
        $this->manager->expects(self::once())->method('flush');

        $participation = $this->participationManager->participate($this->ride, $status);

        self::assertSame($this->ride, $participation->getRide());
        self::assertSame($this->user, $participation->getUser());
        self::assertSame($yes, $participation->getGoingYes());
        self::assertSame($maybe, $participation->getGoingMaybe());
        self::assertSame($no, $participation->getGoingNo());
    }

    #[Test]
    public function updatesExistingParticipationInsteadOfCreatingANewOne(): void
    {
        $existing = (new Participation())->setRide($this->ride)->setUser($this->user)->setGoingYes(true)->setGoingMaybe(false)->setGoingNo(false);
        $this->repository->method('findParticipationForUserAndRide')->willReturn($existing);

        $participation = $this->participationManager->participate($this->ride, 'no');

        self::assertSame($existing, $participation);
        self::assertFalse($participation->getGoingYes());
        self::assertTrue($participation->getGoingNo());
    }

    #[Test]
    public function dispatchesCreatedEventAfterFlushing(): void
    {
        $this->repository->method('findParticipationForUserAndRide')->willReturn(null);

        $this->dispatcher->expects(self::once())
            ->method('dispatch')
            ->with(self::isInstanceOf(ParticipationCreatedEvent::class), ParticipationCreatedEvent::NAME)
            ->willReturnArgument(0);

        $this->participationManager->participate($this->ride, 'yes');
    }
}
