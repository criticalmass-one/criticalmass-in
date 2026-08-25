<?php declare(strict_types=1);

namespace Tests\Command\Statistic;

use App\Command\Statistic\CalculateRideEstimatesCommand;
use App\Criticalmass\Statistic\RideEstimateHandler\RideEstimateHandlerInterface;
use App\Entity\City;
use App\Entity\Ride;
use App\Repository\RideRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class CalculateRideEstimatesCommandTest extends TestCase
{
    #[Test]
    public function recalculatesEveryRideWithoutIntermediateFlushes(): void
    {
        $city = (new City())->setCity('Hamburg');
        $rides = [
            (new Ride())->setCity($city)->setDateTime(new \DateTime('2024-05-31 19:00:00'))->setEstimatedParticipants(120),
            (new Ride())->setCity($city)->setDateTime(new \DateTime('2024-06-28 19:00:00'))->setEstimatedParticipants(80),
        ];

        $repository = $this->createMock(RideRepository::class);
        $repository->method('findAll')->willReturn($rides);

        $manager = $this->createMock(ObjectManager::class);
        $manager->expects(self::once())->method('flush');

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getRepository')->with(Ride::class)->willReturn($repository);
        $registry->method('getManager')->willReturn($manager);

        $handledRides = [];
        $handler = $this->createMock(RideEstimateHandlerInterface::class);
        $handler->method('setRide')->willReturnCallback(function (Ride $ride) use (&$handledRides, $handler): RideEstimateHandlerInterface {
            $handledRides[] = $ride;

            return $handler;
        });
        $handler->expects(self::exactly(2))->method('flushEstimates')->with(false)->willReturnSelf();
        $handler->expects(self::exactly(2))->method('calculateEstimates')->with(false)->willReturnSelf();

        $application = new Application();
        $application->addCommand(new CalculateRideEstimatesCommand($handler, $registry));
        $tester = new CommandTester($application->find('criticalmass:rideestimate:recalculate'));

        $tester->execute([]);

        self::assertSame(0, $tester->getStatusCode());
        self::assertSame($rides, $handledRides);
        self::assertStringContainsString('2024-05-31 19:00', $tester->getDisplay());
        self::assertStringContainsString('120', $tester->getDisplay());
    }

    #[Test]
    public function noRidesStillFlushesAndSucceeds(): void
    {
        $repository = $this->createMock(RideRepository::class);
        $repository->method('findAll')->willReturn([]);

        $manager = $this->createMock(ObjectManager::class);
        $manager->expects(self::once())->method('flush');

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getRepository')->willReturn($repository);
        $registry->method('getManager')->willReturn($manager);

        $handler = $this->createMock(RideEstimateHandlerInterface::class);
        $handler->expects(self::never())->method('setRide');

        $application = new Application();
        $application->addCommand(new CalculateRideEstimatesCommand($handler, $registry));
        $tester = new CommandTester($application->find('criticalmass:rideestimate:recalculate'));

        $tester->execute([]);

        self::assertSame(0, $tester->getStatusCode());
    }
}
