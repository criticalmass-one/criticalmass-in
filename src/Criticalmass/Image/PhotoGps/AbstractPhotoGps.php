<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoGps;

use App\Criticalmass\Geo\GpxReader\TrackReader;
use App\Criticalmass\Geo\Loop\LoopInterface;
use App\Criticalmass\Image\ExifWrapper\ExifWrapperInterface;
use App\Entity\Photo;
use App\Entity\Track;

abstract class AbstractPhotoGps implements PhotoGpsInterface
{
    /** @var Track $track */
    protected $track;

    /** @var Photo $photo */
    protected $photo;

    /** @var array $exifData */
    protected $exifData;

    /** @var TrackReader $trackReader */
    protected $trackReader;

    /** @var \DateTimeZone */
    protected $dateTimeZone;

    /** @var ExifWrapperInterface $exifWrapper */
    protected $exifWrapper;

    /** @var LoopInterface $loop */
    protected $loop;

    public function __construct(TrackReader $trackReader, ExifWrapperInterface $exifWrapper, LoopInterface $loop)
    {
        $this->trackReader = $trackReader;
        $this->exifWrapper = $exifWrapper;
        $this->loop = $loop;
    }

    public function setDateTimeZone(\DateTimeZone $dateTimeZone = null): PhotoGpsInterface
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

    public function setTrack(Track $track = null): PhotoGpsInterface
    {
        $this->track = $track;

        return $this;
    }
}
