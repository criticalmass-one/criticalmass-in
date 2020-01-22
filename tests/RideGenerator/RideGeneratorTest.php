<?php declare(strict_types=1);

namespace Tests\RideGenerator;

use App\Criticalmass\RideGenerator\RideCalculator\FrankfurtRideCalculator;
use App\Criticalmass\RideGenerator\RideGenerator\RideGenerator;
use App\Criticalmass\RideGenerator\RideGenerator\RideGeneratorInterface;
use App\Criticalmass\RideNamer\GermanCityDateRideNamer;
use App\Criticalmass\RideNamer\RideNamerList;
use App\Entity\City;
use App\Entity\CityCycle;
use App\Entity\Ride;
use App\Repository\CityCycleRepository;
use App\Repository\RideRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\RegistryInterface;

class RideGeneratorTest extends TestCase
{
    public function testRideGeneratorForHamburgInJune2011(): void
    {
        $dateTime = new \DateTime('2011-06');

        $hamburg = new City();
        $hamburg->setTitle('Critical Mass Hamburg');

        $rideGenerator = $this->createPreparedRideGeneratorFor($hamburg, $this->createCityCycleForHamburg($hamburg));

        $rideList = $rideGenerator
            ->setDateTime($dateTime)
            ->addCity($hamburg)
            ->execute()
            ->getRideList();

        $this->assertCount(1, $rideList);

        /** @var Ride $ride */
        $ride = array_pop($rideList);

        $this->assertEquals(new \DateTime('2011-06-24 19:00:00'), $ride->getDateTime());
        $this->assertEquals('Moorweide', $ride->getLocation());
        $this->assertEquals('53.562619', $ride->getLatitude());
        $this->assertEquals('9.992445', $ride->getLongitude());
        $this->assertEquals('Critical Mass Hamburg 24.06.2011', $ride->getTitle());
    }

    public function testRideGeneratorForHamburgInSummer2011(): void
    {
        $dateTimeList = [
            new \DateTime('2011-06'),
            new \DateTime('2011-07'),
            new \DateTime('2011-08'),
        ];

        $hamburg = new City();
        $hamburg->setTitle('Critical Mass Hamburg');

        $rideGenerator = $this->createPreparedRideGeneratorFor($hamburg, $this->createCityCycleForHamburg($hamburg));

        $rideList = $rideGenerator
            ->setDateTimeList($dateTimeList)
            ->addCity($hamburg)
            ->execute()
            ->getRideList();

        $this->assertCount(3, $rideList);

        /** @var Ride $ride */
        $ride = array_pop($rideList);

        $this->assertEquals(new \DateTime('2011-08-26 19:00:00'), $ride->getDateTime());
        $this->assertEquals('Moorweide', $ride->getLocation());
        $this->assertEquals('53.562619', $ride->getLatitude());
        $this->assertEquals('9.992445', $ride->getLongitude());
        $this->assertEquals('Critical Mass Hamburg 26.08.2011', $ride->getTitle());

        $ride = array_pop($rideList);

        $this->assertEquals(new \DateTime('2011-07-29 19:00:00'), $ride->getDateTime());
        $this->assertEquals('Moorweide', $ride->getLocation());
        $this->assertEquals('53.562619', $ride->getLatitude());
        $this->assertEquals('9.992445', $ride->getLongitude());
        $this->assertEquals('Critical Mass Hamburg 29.07.2011', $ride->getTitle());

        $ride = array_pop($rideList);

        $this->assertEquals(new \DateTime('2011-06-24 19:00:00'), $ride->getDateTime());
        $this->assertEquals('Moorweide', $ride->getLocation());
        $this->assertEquals('53.562619', $ride->getLatitude());
        $this->assertEquals('9.992445', $ride->getLongitude());
        $this->assertEquals('Critical Mass Hamburg 24.06.2011', $ride->getTitle());
    }

    public function testRideGeneratorFor7RidesInHamburgIn2011(): void
    {
        $dateTimeList = [
            new \DateTime('2011-01'),
            new \DateTime('2011-02'),
            new \DateTime('2011-03'),
            new \DateTime('2011-04'),
            new \DateTime('2011-05'),
            new \DateTime('2011-06'),
            new \DateTime('2011-07'),
            new \DateTime('2011-08'),
            new \DateTime('2011-09'),
            new \DateTime('2011-10'),
            new \DateTime('2011-11'),
            new \DateTime('2011-12'),
        ];

        $hamburg = new City();
        $hamburg->setTitle('Critical Mass Hamburg');

        $rideGenerator = $this->createPreparedRideGeneratorFor($hamburg, $this->createCityCycleForHamburg($hamburg));

        $rideList = $rideGenerator
            ->setDateTimeList($dateTimeList)
            ->addCity($hamburg)
            ->execute()
            ->getRideList();

        $this->assertCount(7, $rideList);
    }

    public function testNoRideBeforeValidAfterInHamburgAt201102(): void
    {
        $hamburg = new City();
        $hamburg->setTitle('Critical Mass Hamburg');

        $rideList = $this->createPreparedRideGeneratorFor($hamburg, $this->createCityCycleForHamburg($hamburg))
            ->addCity($hamburg)
            ->setDateTime(new \DateTime('2011-02-01'))
            ->execute()
            ->getRideList();

        $this->assertCount(0, $rideList);
    }

    public function testRideGeneratorForFrankfurtInJune2019(): void
    {
        $dateTime = new \DateTime('2019-06');

        $frankfurt = new City();
        $frankfurt->setTitle('Critical Mass Frankfurt');

        $rideGenerator = $this->createPreparedRideGeneratorFor($frankfurt, $this->createCityCycleForFrankfurt($frankfurt));

        $rideList = $rideGenerator
            ->setDateTime($dateTime)
            ->addCity($frankfurt)
            ->execute()
            ->getRideList();

        $this->assertCount(1, $rideList);

        /** @var Ride $ride */
        $ride = array_pop($rideList);

        $this->assertEquals(new \DateTime('2019-06-07 19:00:00'), $ride->getDateTime());
        $this->assertEquals('Opernplatz', $ride->getLocation());
        $this->assertEquals('50.115446', $ride->getLatitude());
        $this->assertEquals('8.671593', $ride->getLongitude());
        $this->assertEquals('Critical Mass Frankfurt 07.06.2019', $ride->getTitle());
    }

    protected function createPreparedRideGeneratorFor(City $city, array $cityCycleList): RideGeneratorInterface
    {
        $rideNamerList = new RideNamerList();
        $rideNamerList->addRideNamer(new GermanCityDateRideNamer());

        $repositoryList = [];

        $cityCycleRepository = $this->createMock(CityCycleRepository::class);
        $cityCycleRepository
            ->method('findByCity')
            ->with($this->equalTo($city), $this->anything(), $this->anything())
            ->will($this->returnValue($cityCycleList));

        $repositoryList[CityCycle::class] = $cityCycleRepository;

        $rideRepository = $this->createMock(RideRepository::class);
        $rideRepository
            ->method('findRidesByCycleInInterval')
            ->will($this->returnValue([]));

        $repositoryList[Ride::class] = $rideRepository;

        $registry = $this->createMock(RegistryInterface::class);

        $registry
            ->method('getRepository')
            ->will($this->returnCallback(function (string $entityFqcn) use ($repositoryList) {
                return $repositoryList[$entityFqcn];
            }));

        return new RideGenerator($registry, $rideNamerList);
    }

    protected function createCityCycleForHamburg(City $city): array
    {
        return [(new CityCycle())
            ->setCity($city)
            ->setDayOfWeek(CityCycle::DAY_FRIDAY)
            ->setWeekOfMonth(CityCycle::WEEK_LAST)
            ->setTime(new \DateTime('19:00'))
            ->setLocation('Moorweide')
            ->setLatitude(53.562619)
            ->setLongitude(9.992445)
            ->setValidFrom(new \DateTime('2011-06-24'))
            ->setValidUntil(new \DateTime('2020-02-24'))];
    }

    protected function createCityCycleForFrankfurt(City $city): array
    {
        return [(new CityCycle())
            ->setCity($city)
            ->setDayOfWeek(CityCycle::DAY_FRIDAY)
            ->setWeekOfMonth(CityCycle::WEEK_LAST)
            ->setTime(new \DateTime('19:00:00'))
            ->setLocation('Opernplatz')
            ->setLatitude(50.115446)
            ->setLongitude(8.671593)
            ->setRideCalculatorFqcn(FrankfurtRideCalculator::class)];
    }
}
