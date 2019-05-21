<?php declare(strict_types=1);

namespace App\Criticalmass\Ical;

use App\Entity\Ride;

class RideIcalGenerator extends AbstractIcalGenerator
{
    public function generateForRide(Ride $ride): RideIcalGenerator
    {
        $timezone = new \DateTimeZone($ride->getCity()->getTimezone());
        $durationInterval = new \DateInterval('PT2H');

        $startDateTime = clone $ride->getDateTime();
        $startDateTime->setTimezone($timezone);

        $endDateTime = clone $ride->getDateTime();
        $endDateTime->setTimezone($timezone)->add($durationInterval);

        $vevent = [
            'SUMMARY' => $ride->getTitle(),
            'DTSTART' => $startDateTime,
            'DTEND'   => $endDateTime,
            'DESCRIPTION' => $ride->getDescription(),
        ];

        if ($ride->getHasLocation() && $ride->getLocation() && $ride->getLatitude() && $ride->getLongitude()) {
            $vevent['LOCATION'] = $ride->getLocation();
            $vevent['GEO'] = sprintf('%f;%f', $ride->getLatitude(), $ride->getLongitude());
        }

        $this->calendar->add($vevent);

        return $this;
    }
}