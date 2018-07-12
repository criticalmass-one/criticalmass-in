<?php

namespace AppBundle\Criticalmass\Calendar\EventProvider;

use CalendR\Event\Provider\ProviderInterface;
use AppBundle\Entity\Ride;
use AppBundle\Criticalmass\Calendar\Event\RideEvent;
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

    protected function convertRideToEvent(Ride $ride): RideEvent
    {
        return new RideEvent($ride);
    }
}
