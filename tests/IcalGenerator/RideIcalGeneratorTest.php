<?php declare(strict_types=1);

namespace Tests\IcalGenerator;

use App\Criticalmass\Ical\RideIcalGenerator;
use App\Entity\Ride;
use PHPUnit\Framework\TestCase;

class RideIcalGeneratorTest extends TestCase
{
    public function testRideWithoutDateTimeAndLocation(): void
    {
        $ride = new Ride();
        $ride->setDateTime(null);

        $rideIcalGenerator = new RideIcalGenerator();
        $rideIcalGenerator->generateForRide($ride);

        $dateTime = new \DateTime();
        $dateTimeString = $dateTime->format('Ymd\THis\Z');

        $expectedContent = "BEGIN:VCALENDAR\r
VERSION:2.0\r
PRODID:-//Sabre//Sabre VObject 4.2.0//EN\r
CALSCALE:GREGORIAN\r
BEGIN:VEVENT\r
DTSTAMP:".$dateTimeString."\r
SUMMARY:\r
DESCRIPTION:\r
END:VEVENT\r
END:VCALENDAR\r\n";

        $actualContent = $rideIcalGenerator->getSerializedContent();

        $this->assertEquals($expectedContent, $actualContent);
    }
}
