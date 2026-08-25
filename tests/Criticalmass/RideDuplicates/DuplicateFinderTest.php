<?php declare(strict_types=1);

namespace Tests\Criticalmass\RideDuplicates;

use App\Criticalmass\RideDuplicates\DuplicateFinder\DuplicateFinder;
use App\Entity\City;
use App\Entity\Ride;
use App\Repository\RideRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Support\EntityIdHelper;

final class DuplicateFinderTest extends TestCase
{
    private function ride(int $id, City $city, string $dateTime): Ride
    {
        $ride = (new Ride())->setCity($city)->setDateTime(new \DateTime($dateTime));
        EntityIdHelper::setId($ride, $id);

        return $ride;
    }

    /**
     * @param list<Ride> $allRides
     * @param list<Ride> $cityRides
     */
    private function finder(array $allRides, array $cityRides = []): DuplicateFinder
    {
        $repository = $this->createMock(RideRepository::class);
        $repository->method('findAll')->willReturn($allRides);
        $repository->method('findRidesForCity')->willReturn($cityRides);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getRepository')->with(Ride::class)->willReturn($repository);

        return new DuplicateFinder($registry);
    }

    #[Test]
    public function ridesOfTheSameCityOnTheSameDayAreGroupedAsDuplicates(): void
    {
        $hamburg = (new City())->setId(1);
        $berlin = (new City())->setId(2);

        $rides = [
            $this->ride(10, $hamburg, '2024-05-31 19:00:00'),
            $this->ride(11, $hamburg, '2024-05-31 19:30:00'),
            $this->ride(12, $hamburg, '2024-06-28 19:00:00'),
            $this->ride(20, $berlin, '2024-05-31 19:00:00'),
        ];

        $duplicates = $this->finder($rides)->findDuplicates();

        self::assertSame(['1-2024-05-31'], array_keys($duplicates));
        self::assertEqualsCanonicalizing([10, 11], array_keys($duplicates['1-2024-05-31']));
    }

    #[Test]
    public function threeRidesOnOneDayFormASingleGroup(): void
    {
        $city = (new City())->setId(1);
        $rides = [
            $this->ride(1, $city, '2024-05-31 18:00:00'),
            $this->ride(2, $city, '2024-05-31 19:00:00'),
            $this->ride(3, $city, '2024-05-31 20:00:00'),
        ];

        $duplicates = $this->finder($rides)->findDuplicates();

        self::assertCount(1, $duplicates);
        self::assertEqualsCanonicalizing([1, 2, 3], array_keys($duplicates['1-2024-05-31']));
    }

    #[Test]
    public function noDuplicatesYieldsEmptyArray(): void
    {
        $city = (new City())->setId(1);
        $rides = [
            $this->ride(1, $city, '2024-05-31 19:00:00'),
            $this->ride(2, $city, '2024-06-01 19:00:00'),
        ];

        self::assertSame([], $this->finder($rides)->findDuplicates());
        self::assertSame([], $this->finder([])->findDuplicates());
    }

    #[Test]
    public function restrictingToACityUsesTheCityQuery(): void
    {
        $city = (new City())->setId(3);
        $cityRides = [
            $this->ride(1, $city, '2024-05-31 19:00:00'),
            $this->ride(2, $city, '2024-05-31 21:00:00'),
        ];

        $repository = $this->createMock(RideRepository::class);
        $repository->expects(self::never())->method('findAll');
        $repository->expects(self::once())->method('findRidesForCity')->with($city)->willReturn($cityRides);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getRepository')->willReturn($repository);

        $duplicates = (new DuplicateFinder($registry))->setCity($city)->findDuplicates();

        self::assertSame(['3-2024-05-31'], array_keys($duplicates));
    }
}
