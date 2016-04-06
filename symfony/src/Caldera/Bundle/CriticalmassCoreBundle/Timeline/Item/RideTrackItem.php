<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item;

use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Track;
use Caldera\Bundle\CriticalmassModelBundle\Entity\User;

class RideTrackItem extends AbstractItem
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
     * @var string $polyline
     */
    protected $polyline;

    /**
     * @var string $polylineColor
     */
    protected $polylineColor;

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

    /**
     * @return string
     */
    public function getPolyline()
    {
        return $this->polyline;
    }

    /**
     * @param string $polyline
     */
    public function setPolyline($polyline)
    {
        $this->polyline = $polyline;
    }

    /**
     * @return string
     */
    public function getPolylineColor()
    {
        return $this->polylineColor;
    }

    /**
     * @param string $polylineColor
     */
    public function setPolylineColor($polylineColor)
    {
        $this->polylineColor = $polylineColor;
    }
}