<?php declare(strict_types=1);

namespace Tests\Entity;

use App\Entity\City;
use App\Entity\Ride;
use App\Entity\Subride;
use PHPUnit\Framework\TestCase;

class SubrideTimeTest extends TestCase
{
    public function testTimestampReadsTheStoredTimeAsLocalTime(): void
    {
        $city = (new City())->setTimezone('Europe/Berlin');
        $ride = (new Ride())->setCity($city);

        $subride = (new Subride())
            ->setRide($ride)
            ->setDateTime(new \DateTime('2026-09-27 14:10:00', new \DateTimeZone('UTC')));

        $this->assertSame((new \DateTime('2026-09-27 12:10:00', new \DateTimeZone('UTC')))->getTimestamp(), $subride->getTimestamp());
    }

    public function testTimestampUsesTheTimezoneOfTheCity(): void
    {
        $city = (new City())->setTimezone('Australia/Sydney');
        $ride = (new Ride())->setCity($city);

        $subride = (new Subride())
            ->setRide($ride)
            ->setDateTime(new \DateTime('2026-01-30 16:30:00', new \DateTimeZone('UTC')));

        $this->assertSame((new \DateTime('2026-01-30 05:30:00', new \DateTimeZone('UTC')))->getTimestamp(), $subride->getTimestamp());
    }
}
