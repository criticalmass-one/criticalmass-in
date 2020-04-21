<?php declare(strict_types=1);

namespace Test\Wikidata\CityTimezoneDetector;

use App\Criticalmass\Wikidata\CityTimezoneDetector\DateTimezoneFactory;
use PHPUnit\Framework\TestCase;
use Tightenco\Collect\Support\Collection;
use Wikidata\Property;

class DateTimezoneFactoryTest extends TestCase
{
    public function testUtc(): void
    {
        $testString = 'UTC';

        $actualDateTimezone = DateTimezoneFactory::createFromWikidataProperty($this->createProperty($testString));
        $expectedDateTimezone = new \DateTimeZone('UTC');

        $this->assertEquals($expectedDateTimezone, $actualDateTimezone);
    }

    public function testEuropeBerlin(): void
    {
        $testString = 'UTC+01:00, UTC+02:00';

        $actualDateTimezone = DateTimezoneFactory::createFromWikidataProperty($this->createProperty($testString));
        $expectedDateTimezone = new \DateTimeZone('Europe/Berlin');

        $this->assertEquals($expectedDateTimezone, $actualDateTimezone);
    }

    public function testEuropeLondon(): void
    {
        $testString = 'UTC±00:00, UTC+01:00, Greenwich Mean Time';

        $actualDateTimezone = DateTimezoneFactory::createFromWikidataProperty($this->createProperty($testString));
        $expectedDateTimezone = new \DateTimeZone('Europe/Berlin');

        $this->assertEquals($expectedDateTimezone, $actualDateTimezone);
    }

    public function testEastern(): void
    {
        $testString = 'Eastern Time Zone';

        $actualDateTimezone = DateTimezoneFactory::createFromWikidataProperty($this->createProperty($testString));
        $expectedDateTimezone = new \DateTimeZone('Europe/Berlin');

        $this->assertEquals($expectedDateTimezone, $actualDateTimezone);
    }

    protected function createProperty(string $dateTimezoneString): Property
    {
        $collection = new Collection(['propLabel' => '', 'prop' => '', 'propValue' => $dateTimezoneString]);
        $property = new Property($collection);

        return $property;
    }
}