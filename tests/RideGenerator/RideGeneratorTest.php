<?php declare(strict_types=1);

namespace Tests\RideGenerator;

use App\Criticalmass\RideGenerator\RideCalculator\RideCalculator;
use App\Criticalmass\RideGenerator\RideGenerator\RideGenerator;
use App\Entity\City;
use App\Entity\CityCycle;
use App\Repository\CityCycleRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\RegistryInterface;

class RideGeneratorTest extends TestCase
{
    public function testRideGenerator(): void
    {
        $dateTime = new \DateTime('2011-06');

        $hamburg = new City();
        $hamburgCycle = new CityCycle();

        $cityCycleRepository = $this->createMock(CityCycleRepository::class);
        $cityCycleRepository
            ->method('findByCity')
            ->with($this->equalTo($hamburg), $this->anything(), $this->anything())
            ->will($this->returnValue([$hamburgCycle]));

        $registry = $this->createMock(RegistryInterface::class);

        $registry->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(CityCycle::class))
            ->will($this->returnValue($cityCycleRepository));

        $rideCalculator = $this->createMock(RideCalculator::class);

        $rideGenerator = new RideGenerator($registry, $rideCalculator);

        $rideList = $rideGenerator
            ->setDateTime($dateTime)
            ->addCity($hamburg)
            ->execute();

    }
}
