<?php declare(strict_types=1);

namespace Tests\Statistic;

use App\Criticalmass\Statistic\RideEstimateConverter\RideEstimateConverter;
use App\Entity\RideEstimate;
use App\Entity\Track;
use Doctrine\Common\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\RegistryInterface;

class RideEstimateConverterTest extends TestCase
{
    public function testEstimateConverter(): void
    {
        $track = new Track();
        $track->setDistance(11.2);

        $expectedEstimate = new RideEstimate();
        $expectedEstimate
            ->setEstimatedDistance(11.2)
            ->setTrack($track);

        $manager = $this->createMock(ObjectManager::class);
        $manager
            ->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($expectedEstimate, 0.1));

        $manager
            ->expects($this->once())
            ->method('flush');

        $registry = $this->createMock(RegistryInterface::class);
        $registry
            ->expects($this->exactly(2))
            ->method('getManager')
            ->will($this->returnValue($manager));

        $converter = new RideEstimateConverter($registry);

        $converter->addEstimateFromTrack($track);
    }

    public function testEstimateConverterWithExistingEstimate(): void
    {
        $track = new Track();
        $track->setDistance(11.2);

        $rideEstimate = new RideEstimate();
        $rideEstimate
            ->setTrack($track)
            ->setEstimatedDistance(11.2);

        $track->setRideEstimate($rideEstimate);

        $registry = $this->createMock(RegistryInterface::class);
        $registry
            ->expects($this->never())
            ->method('getManager');

        $converter = new RideEstimateConverter($registry);

        $converter->addEstimateFromTrack($track);
    }
}
