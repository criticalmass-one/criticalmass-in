<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Item;

use App\Entity\Ride;

class RideParticipationEstimateItem extends AbstractItem
{
    /** @var Ride $ride */
    protected $ride;

    /** @var string $rideTitle */
    protected $rideTitle;

    /** @var int $estimatedParticipants */
    protected $estimatedParticipants;

    public function getRide(): Ride
    {
        return $this->ride;
    }

    public function setRide(Ride $ride): RideParticipationEstimateItem
    {
        $this->ride = $ride;

        return $this;
    }

    public function getRideTitle(): string
    {
        return $this->rideTitle;
    }

    public function setRideTitle(string $rideTitle): RideParticipationEstimateItem
    {
        $this->rideTitle = $rideTitle;

        return $this;
    }

    public function getEstimatedParticipants(): int
    {
        return $this->estimatedParticipants;
    }

    public function setEstimatedParticipants(int $estimatedParticipants): RideParticipationEstimateItem
    {
        $this->estimatedParticipants = $estimatedParticipants;

        return $this;
    }
}
