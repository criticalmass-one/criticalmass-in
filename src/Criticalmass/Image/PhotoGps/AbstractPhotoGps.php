<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoGps;

use App\Criticalmass\Geo\GpxService\GpxServiceInterface;
use App\Criticalmass\Image\ExifWrapper\ExifWrapperInterface;
use App\Entity\Photo;
use App\Entity\Track;

abstract class AbstractPhotoGps implements PhotoGpsInterface
{
    protected ?Track $track = null;
    protected ?Photo $photo = null;
    protected ?\DateTimeZone $dateTimeZone = null;

    public function __construct(
        protected readonly GpxServiceInterface $gpxService,
        protected readonly ExifWrapperInterface $exifWrapper,
    ) {
    }

    public function setDateTimeZone(?\DateTimeZone $dateTimeZone = null): PhotoGpsInterface
    {
        $this->dateTimeZone = $dateTimeZone;

        return $this;
    }

    public function setPhoto(Photo $photo): PhotoGpsInterface
    {
        $this->photo = $photo;

        if ($photo->getCity() && $photo->getCity()->getTimezone() && !$this->dateTimeZone) {
            $timezone = $photo->getCity()->getTimezone();

            $this->dateTimeZone = new \DateTimeZone($timezone);
        }

        return $this;
    }

    public function getPhoto(): Photo
    {
        return $this->photo;
    }

    public function setTrack(?Track $track = null): PhotoGpsInterface
    {
        $this->track = $track;

        return $this;
    }
}
