<?php declare(strict_types=1);

namespace App\Event\RideEstimate;

class RideEstimateDeletedEvent extends AbstractRideEstimateEvent
{
    const NAME = 'ride_estimate.deleted';
}
