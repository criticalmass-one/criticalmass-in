<?php

namespace AppBundle\Criticalmass\Timeline\Item;

use AppBundle\Entity\Ride;

class RideParticipationEstimateItem extends AbstractItem
{
    /**
     * @var string $username
     */
    protected $username;

    /**
     * @var Ride $ride
     */
    protected $ride;

    /**
     * @var string $rideTitle
     */
    protected $rideTitle;

    /**
     * @var integer $estimatedParticipants
     */
    protected $estimatedParticipants;

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): RideParticipationEstimateItem
    {
        $this->username = $username;

        return $this;
    }

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
