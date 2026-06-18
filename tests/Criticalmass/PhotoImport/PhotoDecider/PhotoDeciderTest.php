<?php declare(strict_types=1);

namespace Tests\Criticalmass\PhotoImport\PhotoDecider;

use App\Criticalmass\PhotoImport\PhotoDecider\PhotoDecider;
use App\Entity\PhotoImportCandidate;
use App\Entity\Ride;
use App\Repository\RideRepository;
use PHPUnit\Framework\TestCase;

class PhotoDeciderTest extends TestCase
{
    public function testUndatedGalleryIsParked(): void
    {
        $repository = $this->createMock(RideRepository::class);
        $repository->expects(self::never())->method('findByDate');

        self::assertNull($this->decider($repository)->decideForGallery(null, [$this->candidate()]));
    }

    public function testGalleryWithoutMatchingRideIsParked(): void
    {
        $repository = $this->repositoryReturning([]);

        self::assertNull($this->decider($repository)->decideForGallery(new \DateTime('2024-06-01'), [$this->candidate()]));
    }

    public function testSingleRideWithoutGpsIsMatchedOnDateAlone(): void
    {
        $ride = $this->ride(null, null);
        $repository = $this->repositoryReturning([$ride]);

        self::assertSame($ride, $this->decider($repository)->decideForGallery(new \DateTime('2024-06-01'), [$this->candidate()]));
    }

    public function testSeveralRidesWithoutGpsAreParked(): void
    {
        $repository = $this->repositoryReturning([$this->ride(null, null), $this->ride(null, null)]);

        // No coordinates to disambiguate between two rides on the same day → park.
        self::assertNull($this->decider($repository)->decideForGallery(new \DateTime('2024-06-01'), [$this->candidate()]));
    }

    public function testGpsPicksTheNearestRide(): void
    {
        $farRide = $this->ride(0.0, 0.0);
        $nearRide = $this->ride(52.50, 13.40);
        $repository = $this->repositoryReturning([$farRide, $nearRide]);

        $candidates = [
            $this->candidate(52.51, 13.41),
            $this->candidate(52.49, 13.39),
        ];

        self::assertSame($nearRide, $this->decider($repository)->decideForGallery(new \DateTime('2024-06-01'), $candidates));
    }

    public function testGpsBeyondRadiusIsParked(): void
    {
        // The only ride that day is ~5800 km away from where the photos were taken.
        $repository = $this->repositoryReturning([$this->ride(0.0, 0.0)]);

        self::assertNull($this->decider($repository)->decideForGallery(new \DateTime('2024-06-01'), [$this->candidate(52.50, 13.40)]));
    }

    /**
     * @param list<Ride> $rides
     */
    private function repositoryReturning(array $rides): RideRepository
    {
        $repository = $this->createMock(RideRepository::class);
        $repository->method('findByDate')->willReturn($rides);

        return $repository;
    }

    private function decider(RideRepository $repository): PhotoDecider
    {
        return new PhotoDecider($repository);
    }

    private function ride(?float $latitude, ?float $longitude): Ride
    {
        $ride = $this->createMock(Ride::class);
        $ride->method('getLatitude')->willReturn($latitude);
        $ride->method('getLongitude')->willReturn($longitude);

        return $ride;
    }

    private function candidate(?float $latitude = null, ?float $longitude = null): PhotoImportCandidate
    {
        return (new PhotoImportCandidate())
            ->setLatitude($latitude)
            ->setLongitude($longitude);
    }
}
