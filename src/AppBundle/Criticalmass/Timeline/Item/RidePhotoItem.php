<?php

namespace AppBundle\Criticalmass\Timeline\Item;

use AppBundle\Entity\City;
use AppBundle\Entity\Photo;
use AppBundle\Entity\Ride;

class RidePhotoItem extends AbstractItem
{
    /** @var string $username */
    protected $username;

    /** @var Ride $ride */
    protected $ride;

    /** @var City $city */
    protected $city;

    /** @var string $rideTitle */
    protected $rideTitle;

    /** @var integer $counter */
    protected $counter;

    /** @var Photo $previewPhoto */
    protected $previewPhoto;

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): RidePhotoItem
    {
        $this->username = $username;

        return $this;
    }

    public function getRide(): Ride
    {
        return $this->ride;
    }

    public function setRide(Ride $ride): RidePhotoItem
    {
        $this->ride = $ride;

        return $this;
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function setCity(City $city): RidePhotoItem
    {
        $this->city = $city;

        return $this;
    }

    public function getRideTitle(): string
    {
        return $this->rideTitle;
    }

    public function setRideTitle(string $rideTitle): RidePhotoItem
    {
        $this->rideTitle = $rideTitle;

        return $this;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }

    public function setCounter(int $counter): RidePhotoItem
    {
        $this->counter = $counter;

        return $this;
    }

    public function setPreviewPhoto(Photo $previewPhoto): RidePhotoItem
    {
        $this->previewPhoto = $previewPhoto;

        return $this;
    }

    public function getPreviewPhoto(): Photo
    {
        return $this->previewPhoto;
    }
}
