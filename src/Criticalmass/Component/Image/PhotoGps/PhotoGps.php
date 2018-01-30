<?php

namespace Criticalmass\Component\Image\PhotoGps;

use Criticalmass\Bundle\AppBundle\Entity\Photo;
use Criticalmass\Bundle\AppBundle\Entity\Track;
use Criticalmass\Component\Gps\GpxReader\TrackReader;
use PHPExif\Exif;
use PHPExif\Reader\Reader;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * @deprecated
 */
class PhotoGps
{
    /**
     * @var Track $track
     */
    protected $track;

    /**
     * @var Photo $photo
     */
    protected $photo;

    /**
     * @var array $exifData
     */
    protected $exifData;

    /**
     * @var UploaderHelper $uploaderHelper
     */
    protected $uploaderHelper;

    /**
     * @var string $rootDirectory
     */
    protected $rootDirectory;

    /**
     * @var TrackReader $trackReader
     */
    protected $trackReader;

    /** @var \DateTimeZone */
    protected $dateTimeZone;

    public function __construct(UploaderHelper $uploaderHelper, $rootDirectory, TrackReader $trackReader)
    {
        $this->uploaderHelper = $uploaderHelper;
        $this->rootDirectory = $rootDirectory . '/../web';
        $this->trackReader = $trackReader;
    }

    public function setDateTimeZone(\DateTimeZone $dateTimeZone = null): PhotoGps
    {
        $this->dateTimeZone = $dateTimeZone;
        $this->trackReader->setDateTimeZone($dateTimeZone);

        return $this;
    }

    public function setPhoto(Photo $photo): PhotoGps
    {
        $this->photo = $photo;

        return $this;
    }

    public function getPhoto(): Photo
    {
        return $this->photo;
    }

    public function setTrack(Track $track): PhotoGps
    {
        $this->track = $track;

        return $this;
    }

    public function execute(): PhotoGps
    {
        if ($gps = $this->getExifCoords()) {
            var_dump($gps);

            /** @todo check keys of gps array */

            $this->photo
                ->setLatitude($gps['lat'])
                ->setLongitude($gps['lon'])
            ;
        } elseif ($this->track) {
            $this->approximateCoordinates();
        }

        return $this;
    }

    public function approximateCoordinates(): PhotoGps
    {
        $this->trackReader->loadTrack($this->track);

        $dateTime = $this->getExifDateTime();

        if ($dateTime) {
            $result = $this->trackReader->findCoordNearDateTime($dateTime);

            $this->photo->setLatitude($result['latitude']);
            $this->photo->setLongitude($result['longitude']);
        }

        return $this;
    }

    protected function getExifDateTime(): ?\DateTime
    {
        $exif = $this->readExifData();

        if ($dateTime = $exif->getCreationDate()) {
            return $dateTime;
        }

        return null;
    }

    protected function getExifCoords(): ?array
    {
        $exif = $this->readExifData();

        if ($gps = $exif->getGPS()) {
            return $gps;
        }

        return null;
    }

    protected function readExifData(): Exif
    {
        $filename = $this->uploaderHelper->asset($this->photo, 'imageFile');

        $reader = Reader::factory(Reader::TYPE_NATIVE);
        $exif = $reader->read($filename);

        return $exif;
    }
}
