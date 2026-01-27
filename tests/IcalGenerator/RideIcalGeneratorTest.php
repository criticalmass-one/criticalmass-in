<?php declare(strict_types=1);

namespace Tests\IcalGenerator;

use App\Criticalmass\Ical\RideIcalGenerator;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\Ride;
use PHPUnit\Framework\TestCase;
use Sabre\VObject\Component\VCalendar;

class RideIcalGeneratorTest extends TestCase
{
    protected function createRideIcalGenerator(): RideIcalGenerator
    {
        $calendar = new VCalendar();

        $objectRouter = $this->createMock(ObjectRouterInterface::class);
        $objectRouter->method('generate')->willReturn('https://criticalmass.in/hamburg/2011-06-24');

        return new RideIcalGenerator($calendar, $objectRouter);
    }

    public function testEmptyRide(): void
    {
        $ride = new Ride();
        $ride->setDateTime(new \Carbon\Carbon());

        $rideIcalGenerator = $this->createRideIcalGenerator();
        $rideIcalGenerator->generateForRide($ride);

        $actualContent = $rideIcalGenerator->getSerializedContent();

        $this->assertStringContainsString('BEGIN:VCALENDAR', $actualContent);
        $this->assertStringContainsString('BEGIN:VEVENT', $actualContent);
        $this->assertStringContainsString('END:VEVENT', $actualContent);
        $this->assertStringContainsString('END:VCALENDAR', $actualContent);
    }

    public function testRideWithCity(): void
    {
        $city = new City();
        $city->addSlug(new CitySlug('hamburg'));

        $ride = new Ride();
        $ride
            ->setDateTime(new \Carbon\Carbon('2011-06-24 19:00:00'))
            ->setCity($city);

        $rideIcalGenerator = $this->createRideIcalGenerator();
        $rideIcalGenerator->generateForRide($ride);

        $actualContent = $rideIcalGenerator->getSerializedContent();

        $this->assertStringContainsString('BEGIN:VCALENDAR', $actualContent);
        $this->assertStringContainsString('URL;VALUE=URI:https://criticalmass.in/hamburg/2011-06-24', $actualContent);
    }

    public function testRideWithCityDateTime(): void
    {
        $timezoneSpec = 'Europe/Berlin';
        $rideDateTime = new \Carbon\Carbon('2011-06-24 19:00:00', new \DateTimeZone($timezoneSpec));

        $city = new City();
        $city
            ->addSlug(new CitySlug('hamburg'))
            ->setTimezone($timezoneSpec);

        $ride = new Ride();
        $ride
            ->setDateTime($rideDateTime)
            ->setCity($city);

        $rideIcalGenerator = $this->createRideIcalGenerator();
        $rideIcalGenerator->generateForRide($ride);

        $actualContent = $rideIcalGenerator->getSerializedContent();

        $this->assertStringContainsString('UID:criticalmass-one-hamburg-', $actualContent);
        $this->assertStringContainsString('DTSTART;TZID=Europe/Berlin:20110624T190000', $actualContent);
        $this->assertStringContainsString('DTEND;TZID=Europe/Berlin:20110624T210000', $actualContent);
    }

    public function testRideWithCityDateTimeTitle(): void
    {
        $timezoneSpec = 'Europe/Berlin';
        $rideDateTime = new \Carbon\Carbon('2011-06-24 19:00:00', new \DateTimeZone($timezoneSpec));

        $city = new City();
        $city
            ->addSlug(new CitySlug('hamburg'))
            ->setTimezone($timezoneSpec);

        $ride = new Ride();
        $ride
            ->setDateTime($rideDateTime)
            ->setCity($city)
            ->setTitle('Critical Mass Hamburg im Sommer 2011');

        $rideIcalGenerator = $this->createRideIcalGenerator();
        $rideIcalGenerator->generateForRide($ride);

        $actualContent = $rideIcalGenerator->getSerializedContent();

        $this->assertStringContainsString('SUMMARY:Critical Mass Hamburg im Sommer 2011', $actualContent);
    }

    public function testRideWithCityDateTimeLocation(): void
    {
        $timezoneSpec = 'Europe/Berlin';
        $rideDateTime = new \Carbon\Carbon('2011-06-24 19:00:00', new \DateTimeZone($timezoneSpec));

        $city = new City();
        $city
            ->addSlug(new CitySlug('hamburg'))
            ->setTimezone($timezoneSpec);

        $ride = new Ride();
        $ride
            ->setDateTime($rideDateTime)
            ->setCity($city)
            ->setLocation('Audimax');

        $rideIcalGenerator = $this->createRideIcalGenerator();
        $rideIcalGenerator->generateForRide($ride);

        $actualContent = $rideIcalGenerator->getSerializedContent();

        $this->assertStringContainsString('LOCATION:Audimax', $actualContent);
    }

    public function testRideWithCityDateTimeLocationLatitudeLongitude(): void
    {
        $timezoneSpec = 'Europe/Berlin';
        $rideDateTime = new \Carbon\Carbon('2011-06-24 19:00:00', new \DateTimeZone($timezoneSpec));

        $city = new City();
        $city
            ->addSlug(new CitySlug('hamburg'))
            ->setTimezone($timezoneSpec);

        $ride = new Ride();
        $ride
            ->setDateTime($rideDateTime)
            ->setCity($city)
            ->setLatitude(53.566389)
            ->setLongitude(9.984892)
            ->setLocation('Audimax');

        $rideIcalGenerator = $this->createRideIcalGenerator();
        $rideIcalGenerator->generateForRide($ride);

        $actualContent = $rideIcalGenerator->getSerializedContent();

        $this->assertStringContainsString('LOCATION:Audimax', $actualContent);
        $this->assertStringContainsString('GEO:53.566389;9.984892', $actualContent);
    }

    public function testRideWithCityDateTimeDescription(): void
    {
        $timezoneSpec = 'Europe/Berlin';
        $rideDateTime = new \Carbon\Carbon('2011-06-24 19:00:00', new \DateTimeZone($timezoneSpec));

        $city = new City();
        $city
            ->addSlug(new CitySlug('hamburg'))
            ->setTimezone($timezoneSpec);

        $ride = new Ride();
        $ride
            ->setDateTime($rideDateTime)
            ->setCity($city)
            ->setDescription('Testbeschreibung Foo Bar Baz');

        $rideIcalGenerator = $this->createRideIcalGenerator();
        $rideIcalGenerator->generateForRide($ride);

        $actualContent = $rideIcalGenerator->getSerializedContent();

        $this->assertStringContainsString('DESCRIPTION:Testbeschreibung Foo Bar Baz', $actualContent);
    }
}
