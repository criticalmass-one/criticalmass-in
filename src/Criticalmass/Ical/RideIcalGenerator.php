<?php declare(strict_types=1);

namespace App\Criticalmass\Ical;

use App\Entity\Ride;

class RideIcalGenerator extends AbstractIcalGenerator
{
    public function generateForRide(Ride $ride): RideIcalGenerator
    {
        $vevent = [
            'SUMMARY' => $ride->getTitle(),
            'DESCRIPTION' => $ride->getDescription(),
        ];

        if ($ride->getDateTime()) {
            $timezone = $this->retrieveTimezone($ride);
            $durationInterval = new \DateInterval('PT2H');

            $startDateTime = clone $ride->getDateTime();
            $startDateTime->setTimezone($timezone);

            $endDateTime = clone $ride->getDateTime();
            $endDateTime->setTimezone($timezone)->add($durationInterval);

            $vevent['DTSTART'] = $startDateTime;
            $vevent['DTEND'] = $endDateTime;
        }

        if ($ride->getHasLocation() && $ride->getLocation() && $ride->getLatitude() && $ride->getLongitude()) {
            $vevent['LOCATION'] = $ride->getLocation();
            $vevent['GEO'] = sprintf('%f;%f', $ride->getLatitude(), $ride->getLongitude());
        }

        if ($uid = $this->generateUid($ride)) {
            $vevent->UID = $uid;
        }

        $this->calendar->VEVENT = $vevent;

        if (!$uid) {
            unset($this->calendar->VEVENT->UID);
        }

        return $this;
    }

    protected function retrieveTimezone(Ride $ride): \DateTimeZone
    {
        if ($ride->getCity() && $ride->getCity()->getTimezone()) {
            return new \DateTimeZone($ride->getCity()->getTimezone());
        }

        return new \DateTimeZone('UTC');
    }

    protected function generateUid(Ride $ride): ?string
    {
        if ($ride->getDateTime() && $ride->getCity() && $ride->getCity()->getMainSlug()->getSlug()) {
            return sprintf('criticalmass-one-%s-%s', $ride->getCity()->getMainSlug()->getSlug(), $ride->getDateTime()->format('U'));
        }

        return null;
    }
}