<?php declare(strict_types=1);

namespace App\Event\RideEstimate;

use App\Entity\RideEstimate;
use Symfony\Contracts\EventDispatcher\Event;

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
