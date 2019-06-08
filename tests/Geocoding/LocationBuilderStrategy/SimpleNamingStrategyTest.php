<?php declare(strict_types=1);

namespace Tests\Geocoding\LocationBuilderStrategy;

use App\Criticalmass\Geocoding\LocationBuilderStrategy\SimpleNamingStrategy;
use Geocoder\Model\Address;
use Geocoder\Model\AdminLevel;
use Geocoder\Model\AdminLevelCollection;
use Geocoder\Model\Country;
use PHPUnit\Framework\TestCase;

class SimpleNamingStrategyTest extends TestCase
{
    public function testNoLocationWithoutAdminLevel(): void
    {
        $location = new Address('test', new AdminLevelCollection(), null, null, null, 'Niebuhrstraße', '24118', 'Kiel', 'Ravensberg', new Country('Germany'), 'Europe/Berlin');
        $simpleNamingStrategy = new SimpleNamingStrategy();

        $actualLocation = $simpleNamingStrategy->buildLocation($location);

        $this->assertNull($actualLocation);
    }

    public function testSimpleNamingStrategy(): void
    {
        $adminLevel = new AdminLevel(1, 'Kiel');
        $adminLevelCollection = new AdminLevelCollection([$adminLevel]);

        $location = new Address('test', $adminLevelCollection, null, null, null, 'Niebuhrstraße', '24118', 'Kiel', 'Ravensberg', new Country('Germany'), 'Europe/Berlin');
        $simpleNamingStrategy = new SimpleNamingStrategy();

        $actualLocation = $simpleNamingStrategy->buildLocation($location);

        $expectedCollection = 'Niebuhrstraße, Kiel';

        $this->assertEquals($expectedCollection, $actualLocation);
    }
}