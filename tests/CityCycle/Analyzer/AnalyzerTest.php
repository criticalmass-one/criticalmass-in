<?php declare(strict_types=1);

namespace Tests\CityCycle\Analyzer;

use App\Criticalmass\Cycles\Analyzer\CycleAnalyzer;
use App\Criticalmass\Cycles\Analyzer\CycleAnalyzerModelFactory;
use App\Criticalmass\RideGenerator\RideCalculator\RideCalculator;
use App\Criticalmass\RideGenerator\RideGenerator\CityRideGeneratorInterface;
use App\Criticalmass\RideNamer\GermanCityDateRideNamer;
use App\Criticalmass\RideNamer\RideNamerList;
use App\Entity\City;
use App\Entity\CityCycle;
use App\Entity\Ride;
use App\Repository\CityCycleRepository;
use App\Repository\RideRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\RegistryInterface;

class AnalyzerTest extends TestCase
{
    public function testAnalyzerWithEmptyLists(): void
    {
        $repositoryList = [];

        $cityCycleRepository = $this->createMock(CityCycleRepository::class);
        $cityCycleRepository
            ->method($this->equalTo('findByCity'))
            ->will($this->returnValue([]));

        $repositoryList[CityCycle::class] = $cityCycleRepository;

        $rideRepository = $this->createMock(RideRepository::class);
        $rideRepository
            ->method($this->equalTo('findRides'))
            ->will($this->returnValue([]));

        $repositoryList[Ride::class] = $rideRepository;

        $registry = $this->createMock(RegistryInterface::class);

        $registry
            ->method('getRepository')
            ->will($this->returnCallback(function (string $entityFqcn) use ($repositoryList) {
                return $repositoryList[$entityFqcn];
            }));

        $rideGenerator = $this->createMock(CityRideGeneratorInterface::class);
        $rideNamerList = new RideNamerList();
        $rideNamerList->addRideNamer(new GermanCityDateRideNamer());
        $rideCalculator = new RideCalculator($rideNamerList);
        $analyzerModelFactory = new CycleAnalyzerModelFactory();

        $cycleAnalyzer = new CycleAnalyzer($registry, $rideGenerator, $rideCalculator, $analyzerModelFactory);

        $hamburg = new City();
        $hamburg->setCity('Hamburg');

        $resultList = $cycleAnalyzer->setCity($hamburg)
            ->setStartDateTime(new \DateTime('2019-01-01'))
            ->setEndDateTime(new \DateTime('2019-01-31'))
            ->analyze()
            ->getResultList();

        $this->assertEmpty($resultList);
    }
}