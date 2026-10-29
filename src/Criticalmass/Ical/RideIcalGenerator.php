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
        $this->addRideEvent($ride);

        return $this;
    }

    /** @param Ride[] $rides */
    public function generateForRides(array $rides): RideIcalGenerator
    {
        foreach ($rides as $ride) {
            $this->addRideEvent($ride);
        }

        return $this;
    }

    protected function addRideEvent(Ride $ride): void
    {
        $vevent = $this->calendar->add('VEVENT');

        if ($ride->getTitle()) {
            $vevent->add('SUMMARY', $ride->getTitle());
        }

        if ($ride->getDescription()) {
            $vevent->add('DESCRIPTION', $ride->getDescription());
        }

        if ($ride->getDateTime()) {
            $timezone = $this->retrieveTimezone($ride);
            $durationInterval = new \DateInterval('PT2H');

            $startDateTime = clone $ride->getDateTime();
            $startDateTime->setTimezone($timezone);

            $endDateTime = clone $ride->getDateTime();
            $endDateTime->setTimezone($timezone)->add($durationInterval);

            $vevent->add('DTSTART', $startDateTime);
            $vevent->add('DTEND', $endDateTime);
        }

        if ($ride->getLocation()) {
            $vevent->add('LOCATION', $ride->getLocation());
        }

        if ($ride->getLatitude() && $ride->getLongitude()) {
            $vevent->add('GEO', sprintf('%f;%f', $ride->getLatitude(), $ride->getLongitude()));
        }

        if ($uid = $this->generateUid($ride)) {
            $vevent->UID = $uid;
        }

        $vevent->add('URL', $this->objectRouter->generate($ride, null, [], Router::ABSOLUTE_URL));
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