<?php

namespace AppBundle\Criticalmass\Timeline\Item;

use AppBundle\Entity\Ride;

class RideEditItem extends AbstractItem
{
    /** @var string $username */
    protected $username;

    /** @var Ride $ride */
    protected $ride;

    /** @var string $rideTitle */
    protected $rideTitle;

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): RideEditItem
    {
        $this->username = $username;

        return $this;
    }

    public function getRide(): Ride
    {
        return $this->ride;
    }

    public function setRide(Ride $ride): RideEditItem
    {
        $this->ride = $ride;

        return $this;
    }

    public function getRideTitle(): string
    {
        return $this->rideTitle;
    }

    public function setRideTitle(string $rideTitle): RideEditItem
    {
        $this->rideTitle = $rideTitle;

        return $this;
    }
}
