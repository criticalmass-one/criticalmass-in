<?php declare(strict_types=1);

namespace AppBundle\Event\RideEstimate;

class RideEstimateDeletedEvent extends AbstractRideEstimateEvent
{
    const NAME = 'ride_estimate.deleted';
}
