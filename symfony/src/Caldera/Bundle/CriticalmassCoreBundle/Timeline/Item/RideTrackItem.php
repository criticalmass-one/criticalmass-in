<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item;

use Caldera\Bundle\CriticalmassModelBundle\Entity\Photo;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Track;
use Caldera\Bundle\CriticalmassModelBundle\Entity\User;

class RideTrackItem implements ItemInterface
{
    /**
     * @var User $user
     */
    protected $user;

    /**
     * @var Ride $ride
     */
    protected $ride;

    /**
     * @var Track $track
     */
    protected $track;

    /**
     * @var \DateTime $dateTime
     */
    protected $dateTime;

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return Ride
     */
    public function getRide()
    {
        return $this->ride;
    }

    /**
     * @param Ride $ride
     */
    public function setRide($ride)
    {
        $this->ride = $ride;
    }

    /**
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * @param \DateTime $dateTime
     */
    public function setDateTime(\DateTime $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    /**
     * @return Track
     */
    public function getTrack()
    {
        return $this->track;
    }

    /**
     * @param Track $track
     */
    public function setTrack(Track $track)
    {
        $this->track = $track;
    }
}