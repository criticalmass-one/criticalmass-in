<?php declare(strict_types=1);

namespace Criticalmass\Component\Facebook;

use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Component\Facebook\Api\FacebookEventRideApi;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Facebook\GraphNodes\GraphEvent;

class EventSelector
{
    /** @var Doctrine $doctrine */
    protected $doctrine;

    /** @var FacebookEventRideApi $facebookEventRideApi */
    protected $facebookEventRideApi;

    /** @var array $assignedRides */
    protected $assignedRides = [];

    public function __construct(Doctrine $doctrine, FacebookEventRideApi $facebookEventRideApi)
    {
        $this->doctrine = $doctrine;
        $this->facebookEventRideApi = $facebookEventRideApi;
    }

    public function autoselect(): EventSelector
    {
        $rides = $this->doctrine->getRepository(Ride::class )->findFutureRides();

        /** @var Ride $ride */
        foreach ($rides as $ride) {
            if (!$ride->getFacebook()) {
                /** @var GraphEvent $event */
                $event = $this->facebookEventRideApi->getEventForRide($ride);

                if ($event) {
                    $eventId = $event->getId();

                    $link = sprintf('https://www.facebook.com/events/%s', $eventId);

                    $ride->setFacebook($link);

                    $this->assignedRides[] = $ride;
                }
            }
        }

        $this->doctrine->getManager()->flush();

        return $this;
    }

    public function getAssignedRides(): array
    {
        return $this->assignedRides;
    }
}
