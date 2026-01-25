<?php declare(strict_types=1);

namespace App\Criticalmass\Calendar\EventProvider;

use CalendR\Event\Provider\ProviderInterface;
use App\Entity\Ride;
use App\Criticalmass\Calendar\Event\RideEvent;
use Doctrine\Persistence\ManagerRegistry;

readonly class RideProvider implements ProviderInterface
{
    public function __construct(
        private ManagerRegistry $doctrine)
    {
    }

    public function getEvents(\DateTimeInterface $begin, \DateTimeInterface $end, array $options = []): array
    {
        $rideList = $this->findRides($begin, $end);
        $eventList = [];

        foreach ($rideList as $ride) {
            $eventList[] = $this->convertRideToEvent($ride);
        }

        return $eventList;
    }

    protected function findRides(\DateTimeInterface $begin, \DateTimeInterface $end): array
    {
        return $this->doctrine->getRepository(Ride::class)->findRides($begin, $end);
    }

    protected function convertRideToEvent(Ride $ride): RideEvent
    {
        return new RideEvent($ride);
    }
}
