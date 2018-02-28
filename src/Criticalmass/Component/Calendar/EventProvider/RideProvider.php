<?php

namespace Criticalmass\Component\Calendar\EventProvider;

use CalendR\Event\Provider\ProviderInterface;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Component\Calendar\Event\Event;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

class RideProvider implements ProviderInterface
{
    protected $doctrine;

    public function __construct(Doctrine $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getEvents(\DateTime $begin, \DateTime $end, array $options = []): array
    {
        $rideList = $this->findRides($begin, $end);
        $eventList = [];

        foreach ($rideList as $ride) {
            $eventList[] = $this->convertRideToEvent($ride);
        }

        return $eventList;
    }

    protected function findRides(\DateTime $begin, \DateTime $end): array
    {
        return $this->doctrine->getRepository(Ride::class)->findRides($begin, $end);
    }

    protected function convertRideToEvent(Ride $ride): Event
    {
        $event = new Event($ride->getCity()->getCity(), $ride->getDateTime(), $ride->getDateTime());

        return $event;
    }
}
