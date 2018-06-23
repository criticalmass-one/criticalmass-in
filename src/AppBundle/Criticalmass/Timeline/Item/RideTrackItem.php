<?php

namespace AppBundle\Criticalmass\Timeline\Item;

use AppBundle\Entity\Ride;
use AppBundle\Entity\Track;

class RideTrackItem extends AbstractItem
{
    /** @var string $username */
    protected $username;

    /** @var Ride $ride */
    protected $ride;

    /** @var string $rideTitle */
    protected $rideTitle;

    /** @var Track $track */
    protected $track;

    /** @var float $distance */
    protected $distance;

    /** @var float $duration */
    protected $duration;

    /** @var string $polyline */
    protected $polyline;

    /** @var string $polylineColor */
    protected $polylineColor;

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): RideTrackItem
    {
        $this->username = $username;

        return $this;
    }

    public function getRide(): Ride
    {
        return $this->ride;
    }

    public function setRide(Ride $ride): RideTrackItem
    {
        $this->ride = $ride;

        return $this;
    }

    public function getRideTitle(): string
    {
        return $this->rideTitle;
    }

    public function setRideTitle(string $rideTitle): RideTrackItem
    {
        $this->rideTitle = $rideTitle;

        return $this;
    }

    public function getTrack(): Track
    {
        return $this->track;
    }

    public function setTrack(Track $track): RideTrackItem
    {
        $this->track = $track;

        return $this;
    }

    public function getDistance(): float
    {
        return $this->distance;
    }

    public function setDistance(float $distance): RideTrackItem
    {
        $this->distance = $distance;

        return $this;
    }

    public function getDuration(): float
    {
        return $this->duration;
    }

    public function setDuration(float $duration): RideTrackItem
    {
        $this->duration = $duration;

        return $this;
    }

    public function getPolyline(): string
    {
        return $this->polyline;
    }

    public function setPolyline(string $polyline): RideTrackItem
    {
        $this->polyline = $polyline;

        return $this;
    }

    public function getPolylineColor(): string
    {
        return $this->polylineColor;
    }

    public function setPolylineColor(string $polylineColor): RideTrackItem
    {
        $this->polylineColor = $polylineColor;

        return $this;
    }
}
