<?php

namespace AppBundle\Criticalmass\Timeline\Item;

use AppBundle\Entity\Ride;

class RideCommentItem extends AbstractItem
{
    /** @var string $username */
    protected $username;

    /* @var Ride $ride */
    protected $ride;

    /** @var string $rideTitle */
    protected $rideTitle;

    /** @var string $text */
    protected $text;

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): RideCommentItem
    {
        $this->username = $username;

        return $this;
    }

    public function getRide(): Ride
    {
        return $this->ride;
    }

    public function setRide(Ride $ride): RideCommentItem
    {
        $this->ride = $ride;

        return $this;
    }

    public function getRideTitle(): string
    {
        return $this->rideTitle;
    }

    public function setRideTitle(string $rideTitle): RideCommentItem
    {
        $this->rideTitle = $rideTitle;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): RideCommentItem
    {
        $this->text = $text;

        return $this;
    }
}
