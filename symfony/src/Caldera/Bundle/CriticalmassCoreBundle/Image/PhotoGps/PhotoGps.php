<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Image\PhotoGps;

use Caldera\Bundle\CriticalmassCoreBundle\Gps\GpxReader\GpxReader;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\GpxReader\TrackReader;
use Caldera\Bundle\CriticalmassCoreBundle\Image\ExifReader\DateTimeExifReader;
use Caldera\Bundle\CriticalmassCoreBundle\Image\ExifReader\GpsExifReader;
use Caldera\Bundle\CalderaBundle\Entity\Photo;
use Caldera\Bundle\CalderaBundle\Entity\Track;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class PhotoGps {
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

    /**
     * @var GpsExifReader $gpsExifReader
     */
    protected $gpsExifReader;

    /**
     * @var DateTimeExifReader $dateTimeExifReader
     */
    protected $dateTimeExifReader;

    public function __construct(UploaderHelper $uploaderHelper, $rootDirectory, GpsExifReader $gpsExifReader, DateTimeExifReader $dateTimeExifReader, TrackReader $trackReader)
    {
        $this->uploaderHelper = $uploaderHelper;
        $this->rootDirectory = $rootDirectory.'/../web';
        $this->gpsExifReader = $gpsExifReader;
        $this->dateTimeExifReader = $dateTimeExifReader;
        $this->trackReader = $trackReader;
    }

    public function setPhoto(Photo $photo)
    {
        $this->photo = $photo;

        return $this;
    }
    
    public function setTrack(Track $track)
    {
        $this->track = $track;

        return $this;
    }
    
    public function execute()
    {
        $this->gpsExifReader->setPhoto($this->photo);

        if ($this->gpsExifReader->hasGpsExifData()) {
            $this->gpsExifReader->execute();

            $this->photo = $this->gpsExifReader->getPhoto();
        } elseif ($this->track) {
            $this->approximateCoordinates();
        }

        return $this;
    }
    
    protected function readExifData()
    {
        $filename = $this->uploaderHelper->asset($this->photo, 'imageFile');

        $this->exifData = exif_read_data($filename, 0, true);
    }

    protected function xmlToDateTime($xml)
    {
        return new \DateTime(str_replace("T", " ", str_replace("Z", "", $xml)));
    }

    function timeDiffinSec($difference)
    {
        return $difference->format('%s') + 60 * $difference->format("%i") + 3600 * $difference->format("%H");
    }

    function interpolate($firstPoint, $secondPoint, $i, $j) {
        $n = $i + $j;
        return floatval($firstPoint * (($n - $i) / floatval($n))) +
        floatval($secondPoint * (($n - $j) / floatval($n)));
    }

    public function approximateCoordinates()
    {
        $this->trackReader->loadTrack($this->track);

        $dateTime = $this->dateTimeExifReader
            ->setPhoto($this->photo)
            ->execute()
            ->getDateTime();

        $result = $this->trackReader->findCoordNearDateTime($dateTime);

        $this->photo->setLatitude($result['latitude']);
        $this->photo->setLongitude($result['longitude']);
    }
}