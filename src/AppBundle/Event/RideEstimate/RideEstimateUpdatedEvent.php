<?php declare(strict_types=1);

namespace AppBundle\Event\RideEstimate;

class RideEstimateUpdatedEvent extends AbstractRideEstimateEvent
{
    const NAME = 'ride_estimate.updated';
}
