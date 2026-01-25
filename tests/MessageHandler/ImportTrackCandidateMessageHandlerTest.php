<?php declare(strict_types=1);

namespace Tests\MessageHandler;

use App\Criticalmass\MassTrackImport\ProposalPersister\ProposalPersisterInterface;
use App\Criticalmass\MassTrackImport\TrackDecider\RideResult;
use App\Criticalmass\MassTrackImport\TrackDecider\TrackDeciderInterface;
use App\Entity\Ride;
use App\Entity\TrackImportCandidate;
use App\Entity\User;
use App\Message\ImportTrackCandidateMessage;
use App\MessageHandler\ImportTrackCandidateMessageHandler;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

class ImportTrackCandidateMessageHandlerTest extends TestCase
{
    public function testHandleMessageWithMatchingRide(): void
    {
        $user = new User();
        $user->setId(42);

        $userRepository = $this->createMock(ObjectRepository::class);
        $userRepository
            ->expects($this->once())
            ->method('find')
            ->with(42)
            ->willReturn($user);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry
            ->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($userRepository);

        $ride = new Ride();
        $rideResult = $this->createMock(RideResult::class);

        $trackDecider = $this->createMock(TrackDeciderInterface::class);
        $trackDecider
            ->expects($this->once())
            ->method('decide')
            ->with($this->callback(function (TrackImportCandidate $candidate) use ($user) {
                return $candidate->getUser() === $user
                    && $candidate->getActivityId() === 123456789
                    && $candidate->getName() === 'Critical Mass Hamburg'
                    && $candidate->getDistance() === 25.5
                    && $candidate->getElapsedTime() === 7200
                    && $candidate->getType() === 'Ride'
                    && $candidate->getStartLatitude() === 53.5511
                    && $candidate->getStartLongitude() === 9.9937
                    && $candidate->getEndLatitude() === 53.5611
                    && $candidate->getEndLongitude() === 10.0037
                    && $candidate->getPolyline() === 'encodedPolylineString';
            }))
            ->willReturn($rideResult);

        $proposalPersister = $this->createMock(ProposalPersisterInterface::class);
        $proposalPersister
            ->expects($this->once())
            ->method('persist')
            ->with($rideResult);

        $message = new ImportTrackCandidateMessage(
            userId: 42,
            activityId: 123456789,
            name: 'Critical Mass Hamburg',
            distance: 25.5,
            elapsedTime: 7200,
            type: 'Ride',
            startDateTime: new \DateTime('2024-01-15 18:00:00'),
            startLatitude: 53.5511,
            startLongitude: 9.9937,
            endLatitude: 53.5611,
            endLongitude: 10.0037,
            polyline: 'encodedPolylineString'
        );

        $handler = new ImportTrackCandidateMessageHandler($registry, $proposalPersister, $trackDecider);
        $handler($message);
    }

    public function testHandleMessageWithNoMatchingRide(): void
    {
        $user = new User();
        $user->setId(42);

        $userRepository = $this->createMock(ObjectRepository::class);
        $userRepository
            ->expects($this->once())
            ->method('find')
            ->with(42)
            ->willReturn($user);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry
            ->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($userRepository);

        $trackDecider = $this->createMock(TrackDeciderInterface::class);
        $trackDecider
            ->expects($this->once())
            ->method('decide')
            ->willReturn(null);  // No matching ride found

        $proposalPersister = $this->createMock(ProposalPersisterInterface::class);
        $proposalPersister
            ->expects($this->never())
            ->method('persist');

        $message = new ImportTrackCandidateMessage(
            userId: 42,
            activityId: 123456789,
            name: 'Random Activity',
            distance: 10.0,
            elapsedTime: 3600,
            type: 'Run',
            startDateTime: new \DateTime('2024-01-15 10:00:00'),
            startLatitude: 50.0,
            startLongitude: 10.0,
            endLatitude: 50.1,
            endLongitude: 10.1,
            polyline: 'abc'
        );

        $handler = new ImportTrackCandidateMessageHandler($registry, $proposalPersister, $trackDecider);
        $handler($message);
    }

    public function testHandleMessageWithNonExistentUser(): void
    {
        $userRepository = $this->createMock(ObjectRepository::class);
        $userRepository
            ->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);  // User not found

        $registry = $this->createMock(ManagerRegistry::class);
        $registry
            ->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($userRepository);

        $trackDecider = $this->createMock(TrackDeciderInterface::class);
        $trackDecider
            ->expects($this->never())
            ->method('decide');

        $proposalPersister = $this->createMock(ProposalPersisterInterface::class);
        $proposalPersister
            ->expects($this->never())
            ->method('persist');

        $message = new ImportTrackCandidateMessage(
            userId: 999,
            activityId: 123456789,
            name: 'Test Activity',
            distance: 10.0,
            elapsedTime: 3600,
            type: 'Ride',
            startDateTime: new \DateTime(),
            startLatitude: 50.0,
            startLongitude: 10.0,
            endLatitude: 50.1,
            endLongitude: 10.1,
            polyline: 'abc'
        );

        $handler = new ImportTrackCandidateMessageHandler($registry, $proposalPersister, $trackDecider);
        $handler($message);
    }

    public function testCandidateHasCorrectStartDateTime(): void
    {
        $user = new User();
        $user->setId(1);

        $userRepository = $this->createMock(ObjectRepository::class);
        $userRepository
            ->method('find')
            ->willReturn($user);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry
            ->method('getRepository')
            ->willReturn($userRepository);

        $expectedDateTime = new \DateTime('2024-06-28 18:30:00');

        $trackDecider = $this->createMock(TrackDeciderInterface::class);
        $trackDecider
            ->expects($this->once())
            ->method('decide')
            ->with($this->callback(function (TrackImportCandidate $candidate) use ($expectedDateTime) {
                return $candidate->getStartDateTime()->format('Y-m-d H:i:s') === $expectedDateTime->format('Y-m-d H:i:s');
            }))
            ->willReturn(null);

        $proposalPersister = $this->createMock(ProposalPersisterInterface::class);

        $message = new ImportTrackCandidateMessage(
            userId: 1,
            activityId: 1,
            name: 'Test',
            distance: 10.0,
            elapsedTime: 3600,
            type: 'Ride',
            startDateTime: $expectedDateTime,
            startLatitude: 50.0,
            startLongitude: 10.0,
            endLatitude: 50.1,
            endLongitude: 10.1,
            polyline: 'abc'
        );

        $handler = new ImportTrackCandidateMessageHandler($registry, $proposalPersister, $trackDecider);
        $handler($message);
    }
}
