<?php declare(strict_types=1);

namespace App\Event\RideEstimate;

class RideEstimateCreatedEvent extends AbstractRideEstimateEvent
{
    const NAME = 'ride_estimate.created';
}
