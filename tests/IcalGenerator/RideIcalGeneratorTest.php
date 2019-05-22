<?php declare(strict_types=1);

namespace Tests\IcalGenerator;

use App\Criticalmass\Ical\RideIcalGenerator;
use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\Ride;
use PHPUnit\Framework\TestCase;

class RideIcalGeneratorTest extends TestCase
{
    public function testEmptyRide(): void
    {
        $ride = new Ride();
        $ride->setDateTime(null);

        $rideIcalGenerator = new RideIcalGenerator();
        $rideIcalGenerator->generateForRide($ride);

        $dtStamp = new \DateTime();
        $dtStampString = $dtStamp->format('Ymd\THis\Z');

        $expectedContent = "BEGIN:VCALENDAR\r
VERSION:2.0\r
PRODID:-//Sabre//Sabre VObject 4.2.0//EN\r
CALSCALE:GREGORIAN\r
BEGIN:VEVENT\r
DTSTAMP:".$dtStampString."\r
SUMMARY:\r
DESCRIPTION:\r
END:VEVENT\r
END:VCALENDAR\r\n";

        $actualContent = $rideIcalGenerator->getSerializedContent();

        $this->assertEquals($expectedContent, $actualContent);
    }

    public function testRideWithCity(): void
    {
        $city = new City();
        $city->addSlug(new CitySlug('hamburg'));

        $ride = new Ride();
        $ride
            ->setDateTime(null)
            ->setCity($city);

        $rideIcalGenerator = new RideIcalGenerator();
        $rideIcalGenerator->generateForRide($ride);

        $dtStamp = new \DateTime();
        $dtStampString = $dtStamp->format('Ymd\THis\Z');

        $expectedContent = "BEGIN:VCALENDAR\r
VERSION:2.0\r
PRODID:-//Sabre//Sabre VObject 4.2.0//EN\r
CALSCALE:GREGORIAN\r
BEGIN:VEVENT\r
DTSTAMP:".$dtStampString."\r
SUMMARY:\r
DESCRIPTION:\r
END:VEVENT\r
END:VCALENDAR\r\n";

        $actualContent = $rideIcalGenerator->getSerializedContent();

        $this->assertEquals($expectedContent, $actualContent);
    }

    public function testRideWithCityAndDateTime(): void
    {
        $timezoneSpec = 'Europe/Berlin';
        $rideDateTime = new \DateTime('2011-06-24 19:00:00', new \DateTimeZone($timezoneSpec));

        $city = new City();
        $city
            ->addSlug(new CitySlug('hamburg'))
            ->setTimezone($timezoneSpec);

        $ride = new Ride();
        $ride
            ->setDateTime($rideDateTime)
            ->setCity($city);

        $rideIcalGenerator = new RideIcalGenerator();
        $rideIcalGenerator->generateForRide($ride);
        
        $dtStamp = new \DateTime();
        $dtStampString = $dtStamp->format('Ymd\THis\Z');

        $expectedContent = "BEGIN:VCALENDAR\r
VERSION:2.0\r
PRODID:-//Sabre//Sabre VObject 4.2.0//EN\r
CALSCALE:GREGORIAN\r
BEGIN:VEVENT\r
UID:criticalmass-one-hamburg-1308934800\r
DTSTAMP:".$dtStampString."\r
SUMMARY:\r
DESCRIPTION:\r
DTSTART;TZID=Europe/Berlin:20110624T190000\r
DTEND;TZID=Europe/Berlin:20110624T210000\r
END:VEVENT\r
END:VCALENDAR\r\n";

        $actualContent = $rideIcalGenerator->getSerializedContent();

        $this->assertEquals($expectedContent, $actualContent);
    }
}
