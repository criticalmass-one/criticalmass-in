<?php declare(strict_types=1);

namespace Tests\Criticalmass\MassTrackImport;

use App\Criticalmass\MassTrackImport\ActivityLoader\ActivityLoaderInterface;
use App\Criticalmass\MassTrackImport\MassTrackImporter;
use App\Entity\User;
use App\Message\ImportTrackCandidateMessage;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class MassTrackImporterTest extends TestCase
{
    private function createStravaActivity(int $id, string $name): array
    {
        return [
            'id' => $id,
            'name' => $name,
            'distance' => 25000.0,
            'elapsed_time' => 7200,
            'start_date' => '2024-01-15T18:00:00Z',
            'start_latlng' => [53.5511, 9.9937],
            'end_latlng' => [53.5611, 10.0037],
            'type' => 'Ride',
            'map' => [
                'summary_polyline' => 'encodedPolylineString'
            ]
        ];
    }

    public function testExecuteWithSingleActivity(): void
    {
        $user = new User();
        $user->setId(42);

        $token = $this->createMock(UsernamePasswordToken::class);
        $token
            ->method('getUser')
            ->willReturn($user);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->method('getToken')
            ->willReturn($token);

        $activityLoader = $this->createMock(ActivityLoaderInterface::class);
        $activityLoader
            ->expects($this->once())
            ->method('load')
            ->willReturn([
                $this->createStravaActivity(123456789, 'Critical Mass Hamburg')
            ]);

        $messageBus = $this->createMock(MessageBusInterface::class);
        $messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function ($message) {
                return $message instanceof ImportTrackCandidateMessage
                    && $message->getUserId() === 42
                    && $message->getActivityId() === 123456789
                    && $message->getName() === 'Critical Mass Hamburg'
                    && $message->getDistance() === 25000.0
                    && $message->getElapsedTime() === 7200
                    && $message->getType() === 'Ride'
                    && $message->getStartLatitude() === 53.5511
                    && $message->getStartLongitude() === 9.9937
                    && $message->getEndLatitude() === 53.5611
                    && $message->getEndLongitude() === 10.0037
                    && $message->getPolyline() === 'encodedPolylineString';
            }))
            ->willReturn(new Envelope(new \stdClass()));

        $importer = new MassTrackImporter($messageBus, $activityLoader, $tokenStorage);
        $result = $importer->execute();

        $this->assertIsArray($result);
    }

    public function testExecuteWithMultipleActivities(): void
    {
        $user = new User();
        $user->setId(1);

        $token = $this->createMock(UsernamePasswordToken::class);
        $token
            ->method('getUser')
            ->willReturn($user);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->method('getToken')
            ->willReturn($token);

        $activityLoader = $this->createMock(ActivityLoaderInterface::class);
        $activityLoader
            ->expects($this->once())
            ->method('load')
            ->willReturn([
                $this->createStravaActivity(1, 'Activity 1'),
                $this->createStravaActivity(2, 'Activity 2'),
                $this->createStravaActivity(3, 'Activity 3'),
            ]);

        $messageBus = $this->createMock(MessageBusInterface::class);
        $messageBus
            ->expects($this->exactly(3))
            ->method('dispatch')
            ->willReturn(new Envelope(new \stdClass()));

        $importer = new MassTrackImporter($messageBus, $activityLoader, $tokenStorage);
        $importer->execute();
    }

    public function testExecuteWithNoActivities(): void
    {
        $user = new User();
        $user->setId(1);

        $token = $this->createMock(UsernamePasswordToken::class);
        $token
            ->method('getUser')
            ->willReturn($user);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->method('getToken')
            ->willReturn($token);

        $activityLoader = $this->createMock(ActivityLoaderInterface::class);
        $activityLoader
            ->expects($this->once())
            ->method('load')
            ->willReturn([]);

        $messageBus = $this->createMock(MessageBusInterface::class);
        $messageBus
            ->expects($this->never())
            ->method('dispatch');

        $importer = new MassTrackImporter($messageBus, $activityLoader, $tokenStorage);
        $result = $importer->execute();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testSetStartDateTime(): void
    {
        $user = new User();
        $user->setId(1);

        $token = $this->createMock(UsernamePasswordToken::class);
        $token
            ->method('getUser')
            ->willReturn($user);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->method('getToken')
            ->willReturn($token);

        $startDateTime = new \DateTime('2024-01-01');

        $activityLoader = $this->createMock(ActivityLoaderInterface::class);
        $activityLoader
            ->expects($this->once())
            ->method('setStartDateTime')
            ->with($startDateTime)
            ->willReturnSelf();

        $messageBus = $this->createMock(MessageBusInterface::class);

        $importer = new MassTrackImporter($messageBus, $activityLoader, $tokenStorage);
        $result = $importer->setStartDateTime($startDateTime);

        $this->assertSame($importer, $result);
    }

    public function testSetEndDateTime(): void
    {
        $user = new User();
        $user->setId(1);

        $token = $this->createMock(UsernamePasswordToken::class);
        $token
            ->method('getUser')
            ->willReturn($user);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->method('getToken')
            ->willReturn($token);

        $endDateTime = new \DateTime('2024-12-31');

        $activityLoader = $this->createMock(ActivityLoaderInterface::class);
        $activityLoader
            ->expects($this->once())
            ->method('setEndDateTime')
            ->with($endDateTime)
            ->willReturnSelf();

        $messageBus = $this->createMock(MessageBusInterface::class);

        $importer = new MassTrackImporter($messageBus, $activityLoader, $tokenStorage);
        $result = $importer->setEndDateTime($endDateTime);

        $this->assertSame($importer, $result);
    }

    public function testFluentInterface(): void
    {
        $user = new User();
        $user->setId(1);

        $token = $this->createMock(UsernamePasswordToken::class);
        $token
            ->method('getUser')
            ->willReturn($user);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->method('getToken')
            ->willReturn($token);

        $activityLoader = $this->createMock(ActivityLoaderInterface::class);
        $activityLoader
            ->method('setStartDateTime')
            ->willReturnSelf();
        $activityLoader
            ->method('setEndDateTime')
            ->willReturnSelf();
        $activityLoader
            ->method('load')
            ->willReturn([]);

        $messageBus = $this->createMock(MessageBusInterface::class);

        $importer = new MassTrackImporter($messageBus, $activityLoader, $tokenStorage);

        // Test fluent interface
        $result = $importer
            ->setStartDateTime(new \DateTime('2024-01-01'))
            ->setEndDateTime(new \DateTime('2024-12-31'))
            ->execute();

        $this->assertIsArray($result);
    }
}
