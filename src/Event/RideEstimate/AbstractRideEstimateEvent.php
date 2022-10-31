<?php declare(strict_types=1);

namespace App\Event\RideEstimate;

use App\Entity\RideEstimate;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractRideEstimateEvent extends Event
{
    public function __construct(protected RideEstimate $rideEstimate)
    {
    }

    public function getRideEstimate(): RideEstimate
    {
        return $this->rideEstimate;
    }
}
