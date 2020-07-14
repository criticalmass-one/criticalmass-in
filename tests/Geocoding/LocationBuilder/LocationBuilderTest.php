<?php declare(strict_types=1);

namespace Tests\Geocoding\LocationBuilder;

use App\Criticalmass\Geocoding\LocationBuilder\LocationBuilder;
use App\Criticalmass\Geocoding\LocationBuilderStrategy\SimpleNamingStrategy;
use Geocoder\Model\Address;
use Geocoder\Model\AdminLevel;
use Geocoder\Model\AdminLevelCollection;
use Geocoder\Model\Coordinates;
use Geocoder\Model\Country;
use PHPUnit\Framework\TestCase;
use Tests\Geocoding\TestGeocodeable;

class LocationBuilderTest extends TestCase
{
    public function testResultWithoutAdminLevel(): void
    {
        $locationBuilder = new LocationBuilder(new SimpleNamingStrategy());

        $testGeocodeable = new TestGeocodeable();

        $adminLevelCollection = new AdminLevelCollection();

        $location = new Address('test', $adminLevelCollection, null, null, null, 'Niebuhrstraße', '24118', 'Kiel', 'Ravensberg', new Country('Germany'), 'Europe/Berlin');

        $actualLocation = $locationBuilder->build($testGeocodeable, $location);

        $expectedLocation = new TestGeocodeable();

        $this->assertEquals($expectedLocation, $actualLocation);
        $this->assertNull($actualLocation->getLatitude());
        $this->assertNull($actualLocation->getLongitude());
        $this->assertNull($actualLocation->getLocation());
    }

    public function testResultWithoutLatLng(): void
    {
        $locationBuilder = new LocationBuilder(new SimpleNamingStrategy());

        $testGeocodeable = new TestGeocodeable();

        $adminLevel = new AdminLevel(1, 'Kiel');
        $adminLevelCollection = new AdminLevelCollection([$adminLevel]);

        $location = new Address('test', $adminLevelCollection, null, null, null, 'Niebuhrstraße', '24118', 'Kiel', 'Ravensberg', new Country('Germany'), 'Europe/Berlin');

        $actualLocation = $locationBuilder->build($testGeocodeable, $location);

        $expectedLocation = new TestGeocodeable();
        $expectedLocation->setLocation('Niebuhrstraße, Kiel');

        $this->assertEquals($expectedLocation, $actualLocation);
        $this->assertNull($actualLocation->getLatitude());
        $this->assertNull($actualLocation->getLongitude());
        $this->assertEquals('Niebuhrstraße, Kiel', $actualLocation->getLocation());
    }

    public function testResultWithLatLngDoesNotAffectLatLngs(): void
    {
        $locationBuilder = new LocationBuilder(new SimpleNamingStrategy());

        $testGeocodeable = new TestGeocodeable();

        $adminLevel = new AdminLevel(1, 'Kiel');
        $adminLevelCollection = new AdminLevelCollection([$adminLevel]);

        $coordinates = new Coordinates(54.343017, 10.129594);

        $location = new Address('test', $adminLevelCollection, $coordinates, null, null, 'Niebuhrstraße', '24118', 'Kiel', 'Ravensberg', new Country('Germany'), 'Europe/Berlin');

        $actualLocation = $locationBuilder->build($testGeocodeable, $location);

        $expectedLocation = new TestGeocodeable();
        $expectedLocation->setLocation('Niebuhrstraße, Kiel');

        $this->assertEquals($expectedLocation, $actualLocation);
        $this->assertNull($actualLocation->getLatitude());
        $this->assertNull($actualLocation->getLongitude());
        $this->assertEquals('Niebuhrstraße, Kiel', $actualLocation->getLocation());
    }

    public function testResultWithLatLngDoesNotChangeLatLngs(): void
    {
        $locationBuilder = new LocationBuilder(new SimpleNamingStrategy());

        $testGeocodeable = new TestGeocodeable();
        $testGeocodeable
            ->setLatitude(53.5)
            ->setLongitude(10.5);

        $adminLevel = new AdminLevel(1, 'Kiel');
        $adminLevelCollection = new AdminLevelCollection([$adminLevel]);

        $coordinates = new Coordinates(54.343017, 10.129594);

        $location = new Address('test', $adminLevelCollection, $coordinates, null, null, 'Niebuhrstraße', '24118', 'Kiel', 'Ravensberg', new Country('Germany'), 'Europe/Berlin');

        $actualLocation = $locationBuilder->build($testGeocodeable, $location);

        $expectedLocation = new TestGeocodeable();
        $expectedLocation
            ->setLatitude(53.5)
            ->setLongitude(10.5)
            ->setLocation('Niebuhrstraße, Kiel');

        $this->assertEquals($expectedLocation, $actualLocation);
        $this->assertEquals(53.5, $actualLocation->getLatitude());
        $this->assertEquals(10.5, $actualLocation->getLongitude());
        $this->assertEquals('Niebuhrstraße, Kiel', $actualLocation->getLocation());
    }
}