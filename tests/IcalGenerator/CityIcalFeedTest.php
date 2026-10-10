<?php declare(strict_types=1);

namespace Tests\IcalGenerator;

use App\Criticalmass\Ical\RideIcalGenerator;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\Ride;
use PHPUnit\Framework\TestCase;
use Sabre\VObject\Component\VCalendar;

class CityIcalFeedTest extends TestCase
{
    protected function createRideIcalGenerator(): RideIcalGenerator
    {
        $calendar = new VCalendar();

        $objectRouter = $this->createMock(ObjectRouterInterface::class);
        $objectRouter->method('generate')->willReturn('https://criticalmass.in/hamburg/2011-06-24');

        return new RideIcalGenerator($calendar, $objectRouter);
    }

    protected function createCity(): City
    {
        $city = new City();
        $city
            ->addSlug(new CitySlug('hamburg'))
            ->setCity('Hamburg')
            ->setTimezone('Europe/Berlin');

        return $city;
    }

    public function testGenerateForMultipleRides(): void
    {
        $city = $this->createCity();

        $ride1 = new Ride();
        $ride1
            ->setDateTime(new \DateTime('2024-01-26 19:00:00', new \DateTimeZone('Europe/Berlin')))
            ->setCity($city)
            ->setTitle('Critical Mass Hamburg Januar 2024');

        $ride2 = new Ride();
        $ride2
            ->setDateTime(new \DateTime('2024-02-23 19:00:00', new \DateTimeZone('Europe/Berlin')))
            ->setCity($city)
            ->setTitle('Critical Mass Hamburg Februar 2024');

        $rideIcalGenerator = $this->createRideIcalGenerator();
        $rideIcalGenerator->generateForRides([$ride1, $ride2]);

        $actualContent = $rideIcalGenerator->getSerializedContent();

        $this->assertStringContainsString('BEGIN:VCALENDAR', $actualContent);
        $this->assertStringContainsString('END:VCALENDAR', $actualContent);

        $this->assertEquals(2, substr_count($actualContent, 'BEGIN:VEVENT'));
        $this->assertEquals(2, substr_count($actualContent, 'END:VEVENT'));

        $this->assertStringContainsString('SUMMARY:Critical Mass Hamburg Januar 2024', $actualContent);
        $this->assertStringContainsString('SUMMARY:Critical Mass Hamburg Februar 2024', $actualContent);
    }

    public function testGenerateForEmptyRideList(): void
    {
        $rideIcalGenerator = $this->createRideIcalGenerator();
        $rideIcalGenerator->generateForRides([]);

        $actualContent = $rideIcalGenerator->getSerializedContent();

        $this->assertStringContainsString('BEGIN:VCALENDAR', $actualContent);
        $this->assertStringContainsString('END:VCALENDAR', $actualContent);
        $this->assertStringNotContainsString('BEGIN:VEVENT', $actualContent);
    }

    public function testMultipleRidesHaveUniqueUids(): void
    {
        $city = $this->createCity();

        $ride1 = new Ride();
        $ride1
            ->setDateTime(new \DateTime('2024-01-26 19:00:00', new \DateTimeZone('Europe/Berlin')))
            ->setCity($city);

        $ride2 = new Ride();
        $ride2
            ->setDateTime(new \DateTime('2024-02-23 19:00:00', new \DateTimeZone('Europe/Berlin')))
            ->setCity($city);

        $rideIcalGenerator = $this->createRideIcalGenerator();
        $rideIcalGenerator->generateForRides([$ride1, $ride2]);

        $actualContent = $rideIcalGenerator->getSerializedContent();

        $this->assertEquals(2, substr_count($actualContent, 'UID:criticalmass-one-hamburg-'));

        preg_match_all('/^UID:(.+)$/m', $actualContent, $matches);
        $this->assertCount(2, $matches[1]);
        $this->assertNotEquals(trim($matches[1][0]), trim($matches[1][1]));
    }

    public function testMultipleRidesContainUrlField(): void
    {
        $city = $this->createCity();

        $ride1 = new Ride();
        $ride1
            ->setDateTime(new \DateTime('2024-01-26 19:00:00', new \DateTimeZone('Europe/Berlin')))
            ->setCity($city);

        $ride2 = new Ride();
        $ride2
            ->setDateTime(new \DateTime('2024-02-23 19:00:00', new \DateTimeZone('Europe/Berlin')))
            ->setCity($city);

        $rideIcalGenerator = $this->createRideIcalGenerator();
        $rideIcalGenerator->generateForRides([$ride1, $ride2]);

        $actualContent = $rideIcalGenerator->getSerializedContent();

        $this->assertEquals(2, substr_count($actualContent, 'URL;VALUE=URI:https://criticalmass.in/hamburg/'));
    }
}
