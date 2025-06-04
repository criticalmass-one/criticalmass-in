<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Item;

use App\Entity\Ride;
use App\Entity\Track;

class RideTrackItem extends AbstractItem
{
    protected ?Ride $ride = null;

    protected ?string $rideTitle = null;

    protected ?Track $track = null;

    protected ?float $distance = null;

    protected ?float $duration = null;

    protected ?string $polyline = null;

    protected ?string $polylineColor = null;

    protected ?bool $rideEnabled = null;

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

    public function setRideEnabled(bool $rideEnabled): RideTrackItem
    {
        $this->rideEnabled = $rideEnabled;

        return $this;
    }

    public function isRideEnabled(): bool
    {
        return $this->rideEnabled;
    }
}
