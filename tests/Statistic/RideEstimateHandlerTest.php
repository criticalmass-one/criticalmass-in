<?php declare(strict_types=1);

namespace Tests\Statistic;

use App\Criticalmass\Statistic\RideEstimateCalculator\RideEstimateCalculator;
use App\Criticalmass\Statistic\RideEstimateHandler\RideEstimateHandler;
use App\Entity\Ride;
use App\Entity\RideEstimate;
use App\Repository\RideEstimateRepository;
use Doctrine\Common\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\RegistryInterface;

class RideEstimateHandlerTest extends TestCase
{
    public function testWithoutEstimates(): void
    {
        $ride = new Ride();

        $repository = $this->createMock(RideEstimateRepository::class);
        $repository
            ->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('findByRide'))
            ->will($this->returnValue([]));

        $objectManager = $this->createMock(ObjectManager::class);
        $objectManager
            ->expects($this->once())
            ->method('flush');

        $registry = $this->createMock(RegistryInterface::class);

        $registry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(RideEstimate::class))
            ->will($this->returnValue($repository));

        $registry
            ->expects($this->once())
            ->method('getManager')
            ->will($this->returnValue($objectManager));

        $rideEstimateCalculator = new RideEstimateCalculator();
        $rideEstimateHandler = new RideEstimateHandler($registry, $rideEstimateCalculator);

        $rideEstimateHandler->setRide($ride)->calculateEstimates();
    }

    public function testFlushEstimates(): void
    {
        $ride = new Ride();
        $ride
            ->setEstimatedDistance(12.3)
            ->setEstimatedDuration(2.3)
            ->setEstimatedParticipants(234);

        $objectManager = $this->createMock(ObjectManager::class);
        $objectManager
            ->expects($this->once())
            ->method('flush');

        $registry = $this->createMock(RegistryInterface::class);
        $registry
            ->expects($this->once())
            ->method('getManager')
            ->will($this->returnValue($objectManager));

        $rideEstimateCalculator = new RideEstimateCalculator();
        $rideEstimateHandler = new RideEstimateHandler($registry, $rideEstimateCalculator);

        $rideEstimateHandler->setRide($ride)->flushEstimates();

        $this->assertEquals(0, $ride->getEstimatedDistance());
        $this->assertEquals(0, $ride->getEstimatedDuration());
        $this->assertEquals(0, $ride->getEstimatedParticipants());
    }

    public function testFlushEstimatesWithoutFlush(): void
    {
        $ride = new Ride();
        $ride
            ->setEstimatedDistance(12.3)
            ->setEstimatedDuration(2.3)
            ->setEstimatedParticipants(234);

        $registry = $this->createMock(RegistryInterface::class);
        $registry
            ->expects($this->never())
            ->method('getManager');

        $rideEstimateCalculator = new RideEstimateCalculator();
        $rideEstimateHandler = new RideEstimateHandler($registry, $rideEstimateCalculator);

        $rideEstimateHandler->setRide($ride)->flushEstimates(false);

        $this->assertEquals(0, $ride->getEstimatedDistance());
        $this->assertEquals(0, $ride->getEstimatedDuration());
        $this->assertEquals(0, $ride->getEstimatedParticipants());
    }
}
