<?php declare(strict_types=1);

namespace App\Event\RideEstimate;

class RideEstimateDeletedEvent extends AbstractRideEstimateEvent
{
    final const NAME = 'ride_estimate.deleted';
}
