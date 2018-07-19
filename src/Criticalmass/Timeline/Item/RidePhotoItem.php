<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Item;

use App\Entity\Photo;
use App\Entity\Ride;

class RidePhotoItem extends AbstractItem
{
    /** @var Ride $ride */
    protected $ride;

    /** @var integer $counter */
    protected $counter;

    /** @var array $previewPhotoList */
    protected $previewPhotoList = [];

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
}
