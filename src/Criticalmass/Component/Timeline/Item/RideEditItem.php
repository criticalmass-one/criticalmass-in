<?php

namespace Criticalmass\Component\Timeline\Item;

use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Bundle\AppBundle\Entity\User;

class RideEditItem extends AbstractItem
{
    /** @var User $user */
    protected $user;

    /** @var Ride $ride */
    protected $ride;

    /** @var string $rideTitle */
    protected $rideTitle;

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): RideEditItem
    {
        $this->user = $user;

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
