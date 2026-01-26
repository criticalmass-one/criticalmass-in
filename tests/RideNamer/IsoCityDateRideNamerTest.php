<?php declare(strict_types=1);

namespace Tests\RideNamer;

use App\Criticalmass\RideNamer\IsoCityDateRideNamer;
use App\Entity\City;
use App\Entity\Ride;
use PHPUnit\Framework\TestCase;

class IsoCityDateRideNamerTest extends TestCase
{
    private function createRide(string $cityTitle, string $dateTime): Ride
    {
        $city = $this->createMock(City::class);
        $city->method('getTitle')->willReturn($cityTitle);

        $ride = $this->createMock(Ride::class);
        $ride->method('getCity')->willReturn($city);
        $ride->method('getDateTime')->willReturn(new \DateTime($dateTime));

        return $ride;
    }

    public function testGenerateTitle(): void
    {
        $namer = new IsoCityDateRideNamer();
        $ride = $this->createRide('Hamburg', '2024-06-28 19:00:00');

        $this->assertEquals('Hamburg 2024-06-28', $namer->generateTitle($ride));
    }

    public function testGenerateTitleWithDifferentCity(): void
    {
        $namer = new IsoCityDateRideNamer();
        $ride = $this->createRide('Berlin', '2024-01-15 18:00:00');

        $this->assertEquals('Berlin 2024-01-15', $namer->generateTitle($ride));
    }

    public function testGenerateTitleWithCityContainingSpaces(): void
    {
        $namer = new IsoCityDateRideNamer();
        $ride = $this->createRide('Frankfurt am Main', '2024-12-31 20:00:00');

        $this->assertEquals('Frankfurt am Main 2024-12-31', $namer->generateTitle($ride));
    }

    public function testGenerateTitleWithLeapYearDate(): void
    {
        $namer = new IsoCityDateRideNamer();
        $ride = $this->createRide('Köln', '2024-02-29 19:00:00');

        $this->assertEquals('Köln 2024-02-29', $namer->generateTitle($ride));
    }

    public function testIsoDateFormat(): void
    {
        $namer = new IsoCityDateRideNamer();
        $ride = $this->createRide('München', '2024-03-05 19:00:00');

        $title = $namer->generateTitle($ride);

        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2}$/', $title);
    }
}
