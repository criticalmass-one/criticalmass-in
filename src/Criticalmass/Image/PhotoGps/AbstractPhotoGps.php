<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoGps;

use App\Criticalmass\Image\ExifWrapper\ExifWrapperInterface;
use App\Entity\Photo;
use App\Entity\Track;
use App\Criticalmass\Gps\GpxReader\TrackReader;
use League\Flysystem\FilesystemInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

abstract class AbstractPhotoGps implements PhotoGpsInterface
{
    /** @var Track $track */
    protected $track;

    /** @var Photo $photo */
    protected $photo;

    /** @var array $exifData */
    protected $exifData;

    /** @var UploaderHelper $uploaderHelper */
    protected $uploaderHelper;

    /** @var FilesystemInterface $filesystem */
    protected $filesystem;

    /** @var TrackReader $trackReader */
    protected $trackReader;

    /** @var \DateTimeZone */
    protected $dateTimeZone;

    /** @var ExifWrapperInterface $exifWrapper */
    protected $exifWrapper;

    public function __construct(UploaderHelper $uploaderHelper, TrackReader $trackReader, FilesystemInterface $filesystem, ExifWrapperInterface $exifWrapper)
    {
        $this->uploaderHelper = $uploaderHelper;
        $this->filesystem = $filesystem;
        $this->trackReader = $trackReader;
        $this->exifWrapper = $exifWrapper;
    }

    public function setDateTimeZone(\DateTimeZone $dateTimeZone = null): PhotoGpsInterface
    {
        $this->dateTimeZone = $dateTimeZone;
        $this->trackReader->setDateTimeZone($dateTimeZone);

        return $this;
    }

    public function setPhoto(Photo $photo): PhotoGpsInterface
    {
        $this->photo = $photo;

        if ($photo->getCity() && $photo->getCity()->getTimezone() && !$this->dateTimeZone) {
            $timezone = $photo->getCity()->getTimezone();

            $this->dateTimeZone = new \DateTimeZone($timezone);
            $this->trackReader->setDateTimeZone($this->dateTimeZone);
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
