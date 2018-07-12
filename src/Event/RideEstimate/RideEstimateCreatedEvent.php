<?php declare(strict_types=1);

namespace AppBundle\Event\RideEstimate;

class RideEstimateCreatedEvent extends AbstractRideEstimateEvent
{
    const NAME = 'ride_estimate.created';
}
