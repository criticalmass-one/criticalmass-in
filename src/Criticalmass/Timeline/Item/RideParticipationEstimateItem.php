<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Item;

use App\Entity\Ride;

class RideParticipationEstimateItem extends AbstractItem
{
    protected ?Ride $ride = null;

    protected ?string $rideTitle = null;

    protected ?int $estimatedParticipants = null;

    protected ?bool $rideEnabled = null;

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

    public function setRideEnabled(bool $rideEnabled): RideParticipationEstimateItem
    {
        $this->rideEnabled = $rideEnabled;

        return $this;
    }

    public function isRideEnabled(): bool
    {
        return $this->rideEnabled;
    }
}
