<?php declare(strict_types=1);

namespace App\Event\RideEstimate;

class RideEstimateUpdatedEvent extends AbstractRideEstimateEvent
{
    final const NAME = 'ride_estimate.updated';
}
