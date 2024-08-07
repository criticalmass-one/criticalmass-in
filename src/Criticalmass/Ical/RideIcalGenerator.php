<?php declare(strict_types=1);

namespace App\Criticalmass\Ical;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\Ride;
use Sabre\VObject\Component\VCalendar;
use Symfony\Component\Routing\Router;

class RideIcalGenerator implements RideIcalGeneratorInterface
{
    public function __construct(
        private readonly VCalendar $calendar,
        private readonly ObjectRouterInterface $objectRouter
    )
    {

    }

    public function getSerializedContent(): string
    {
        return $this->calendar->serialize();
    }

    public function generateForRide(Ride $ride): RideIcalGenerator
    {
        $vevent = [];

        if ($ride->getTitle()) {
            $vevent['SUMMARY'] = $ride->getTitle();
        }

        if ($ride->getDescription()) {
            $vevent['DESCRIPTION'] = $ride->getDescription();
        }

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

        if ($ride->getLocation()) {
            $vevent['LOCATION'] = $ride->getLocation();
        }

        if ($ride->getLatitude() && $ride->getLongitude()) {
            $vevent['GEO'] = sprintf('%f;%f', $ride->getLatitude(), $ride->getLongitude());
        }

        if ($uid = $this->generateUid($ride)) {
            $vevent['UID'] = $uid;
        }

        $vevent['URL'] = $this->objectRouter->generate($ride, null, [], Router::ABSOLUTE_URL);

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