<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item;

use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Caldera\Bundle\CalderaBundle\Entity\Track;

class RideTrackItem extends AbstractItem
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
     * @var Track $track
     */
    protected $track;

    /**
     * @var float $distance
     */
    protected $distance;

    /**
     * @var float $duration
     */
    protected $duration;

    /**
     * @var string $polyline
     */
    protected $polyline;

    /**
     * @var string $polylineColor
     */
    protected $polylineColor;

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
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
     * @return string
     */
    public function getRideTitle()
    {
        return $this->rideTitle;
    }

    /**
     * @param string $rideTitle
     */
    public function setRideTitle($rideTitle)
    {
        $this->rideTitle = $rideTitle;
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
     * @return float
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * @param float $distance
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;
    }

    /**
     * @return float
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param float $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
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