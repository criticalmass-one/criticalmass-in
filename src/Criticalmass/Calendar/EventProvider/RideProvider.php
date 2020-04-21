<?php

namespace App\Criticalmass\Calendar\EventProvider;

use CalendR\Event\Provider\ProviderInterface;
use App\Entity\Ride;
use App\Criticalmass\Calendar\Event\RideEvent;
use Doctrine\Persistence\ManagerRegistry;

class RideProvider implements ProviderInterface
{
    /** @var ManagerRegistry $doctrine */
    protected $doctrine;

    public function __construct(ManagerRegistry $doctrine)
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
