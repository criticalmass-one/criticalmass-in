<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Item;

use App\Entity\Ride;
use App\Entity\Photo;

class RidePhotoItem extends AbstractItem
{
    protected ?Ride $ride = null;

    protected ?int $counter = null;

    protected array $previewPhotoList = [];

    protected ?bool $rideEnabled = null;

    public function getRide(): Ride
    {
        return $this->ride;
    }

    public function setRide(Ride $ride): RidePhotoItem
    {
        $this->ride = $ride;

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

    public function addPreviewPhoto(Photo $previewPhoto): RidePhotoItem
    {
        $this->previewPhotoList[] = $previewPhoto;

        return $this;
    }

    public function getPreviewPhotoList(): array
    {
        return $this->previewPhotoList;
    }

    public function setRideEnabled(bool $rideEnabled): RidePhotoItem
    {
        $this->rideEnabled = $rideEnabled;

        return $this;
    }

    public function isRideEnabled(): bool
    {
        return $this->rideEnabled;
    }
}
