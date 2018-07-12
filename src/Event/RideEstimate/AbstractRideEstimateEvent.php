<?php declare(strict_types=1);

namespace AppBundle\Event\RideEstimate;

use AppBundle\Entity\RideEstimate;
use Symfony\Component\EventDispatcher\Event;

abstract class AbstractRideEstimateEvent extends Event
{
    /** @var RideEstimate $rideEstimate */
    protected $rideEstimate;

    public function __construct(RideEstimate $rideEstimate)
    {
        $this->rideEstimate = $rideEstimate;
    }

    public function getRideEstimate(): RideEstimate
    {
        return $this->rideEstimate;
    }
}
