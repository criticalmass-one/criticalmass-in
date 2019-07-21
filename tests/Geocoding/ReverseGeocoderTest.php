<?php declare(strict_types=1);

namespace Tests\Geocoding;

use App\Criticalmass\Geocoding\LocationBuilder\LocationBuilder;
use App\Criticalmass\Geocoding\LocationBuilderStrategy\SimpleNamingStrategy;
use App\Criticalmass\Geocoding\ReverseGeocoder;
use PHPUnit\Framework\TestCase;

class ReverseGeocoderTest extends TestCase
{
    public function testEmptyGeocodeable(): void
    {
        $simpleNamingStrategy = new SimpleNamingStrategy();
        $locationBuilder = new LocationBuilder($simpleNamingStrategy);
        $reverseGeocoder = new ReverseGeocoder($locationBuilder);

        $testGeocodeable = new TestGeocodeable();
        $actualLocation = $reverseGeocoder->query($testGeocodeable);

        $this->assertNull($actualLocation);
    }

    public function testWithLatLng(): void
    {
        $simpleNamingStrategy = new SimpleNamingStrategy();
        $locationBuilder = new LocationBuilder($simpleNamingStrategy);
        $reverseGeocoder = new ReverseGeocoder($locationBuilder);

        $testGeocodeable = new TestGeocodeable();
        $testGeocodeable
            ->setLatitude(53.5)
            ->setLongitude(10.5);

        $actualLocation = $reverseGeocoder->query($testGeocodeable);

        $this->assertNotNull($actualLocation);
    }

    public function testReverse(): void
    {
        $simpleNamingStrategy = new SimpleNamingStrategy();
        $locationBuilder = new LocationBuilder($simpleNamingStrategy);
        $reverseGeocoder = new ReverseGeocoder($locationBuilder);

        $testGeocodeable = new TestGeocodeable();
        $testGeocodeable
            ->setLatitude(53.5)
            ->setLongitude(10.5);

        $actualGeocodeable = $reverseGeocoder->reverseGeocode($testGeocodeable);

        $expectedGeocodeable = new TestGeocodeable();
        $expectedGeocodeable
            ->setLatitude(53.5)
            ->setLongitude(10.5)
            ->setLocation('Eichenweg, Schleswig-Holstein');

        $this->assertEquals($expectedGeocodeable, $actualGeocodeable);
    }
}