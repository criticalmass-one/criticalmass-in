<?php declare(strict_types=1);

namespace Tests\RideNamer;

use App\Criticalmass\RideNamer\GermanCityDateRideNamer;
use App\Entity\City;
use App\Entity\Ride;
use PHPUnit\Framework\TestCase;

class GermanCityDateRideNamerTest extends TestCase
{
    private function createRide(string $cityTitle, string $dateTime): Ride
    {
        $city = $this->createMock(City::class);
        $city->method('getTitle')->willReturn($cityTitle);

        $ride = $this->createMock(Ride::class);
        $ride->method('getCity')->willReturn($city);
        $ride->method('getDateTime')->willReturn(new \Carbon\Carbon($dateTime));

        return $ride;
    }

    public function testGenerateTitle(): void
    {
        $namer = new GermanCityDateRideNamer();
        $ride = $this->createRide('Hamburg', '2024-06-28 19:00:00');

        $this->assertEquals('Hamburg 28.06.2024', $namer->generateTitle($ride));
    }

    public function testGenerateTitleWithDifferentCity(): void
    {
        $namer = new GermanCityDateRideNamer();
        $ride = $this->createRide('Berlin', '2024-01-15 18:00:00');

        $this->assertEquals('Berlin 15.01.2024', $namer->generateTitle($ride));
    }

    public function testGenerateTitleWithCityContainingSpaces(): void
    {
        $namer = new GermanCityDateRideNamer();
        $ride = $this->createRide('Frankfurt am Main', '2024-12-31 20:00:00');

        $this->assertEquals('Frankfurt am Main 31.12.2024', $namer->generateTitle($ride));
    }

    public function testGermanDateFormat(): void
    {
        $namer = new GermanCityDateRideNamer();
        $ride = $this->createRide('Köln', '2024-03-05 19:00:00');

        $title = $namer->generateTitle($ride);

        $this->assertMatchesRegularExpression('/\d{2}\.\d{2}\.\d{4}$/', $title);
    }

    public function testLeadingZerosInDate(): void
    {
        $namer = new GermanCityDateRideNamer();
        $ride = $this->createRide('München', '2024-01-05 19:00:00');

        $this->assertEquals('München 05.01.2024', $namer->generateTitle($ride));
    }
}
