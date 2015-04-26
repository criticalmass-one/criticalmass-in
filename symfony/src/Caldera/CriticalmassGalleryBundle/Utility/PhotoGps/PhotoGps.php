<?php

namespace Caldera\CriticalmassGalleryBundle\Utility\PhotoGps;

use Caldera\CriticalmassCoreBundle\Utility\GpxReader\GpxReader;
use Caldera\CriticalmassGalleryBundle\Entity\Photo;
use Caldera\CriticalmassGalleryBundle\Utility\ExifReader\GpsReader;
use Caldera\CriticalmassTrackBundle\Entity\Track;

class PhotoGps {
    protected $track;
    protected $photo;
    protected $exifData;
    
    public function __construct()
    {

    }

    public function setPhoto(Photo $photo)
    {
        $this->photo = $photo;
    }
    
    public function setTrack(Track $track)
    {
        $this->track = $track;
    }
    
    public function execute()
    {
        $this->readExifData();
        
        if (isset($this->exifData['GPS']))
        {
            $this->readFromExifData();
        }
        elseif ($this->track)
        {
            $this->approximateCoordinates();
        }
    }
    
    public function readExifData()
    {
        $this->exifData = exif_read_data($this->photo->getFilePath(), 0, true);
    }
    
    public function readFromExifData()
    {
        $gr = new GpsReader($this->photo);
        $gr->execute();
        
        $this->photo->setLatitude($gr->getLatitude());
        $this->photo->setLongitude($gr->getLongitude());
    }

    protected function xmlToDateTime($xml)
    {
        return new \DateTime(str_replace("T", " ", str_replace("Z", "", $xml)));
    }

    function timeDiffinSec($difference) {
        return $difference->format('%s') + 60 * $difference->format("%i") + 3600 * $difference->format("%H");
    }

    function interpolate($firstPoint, $secondPoint, $i, $j) {
        $n = $i + $j;
        return floatval($firstPoint * (($n - $i) / floatval($n))) +
        floatval($secondPoint * (($n - $j) / floatval($n)));
    }

    public function approximateCoordinates()
    {
        $gpxReader = new GpxReader();
        $gpxReader->loadFile('/Users/maltehuebner/Documents/criticalmass.in/criticalmass/symfony/web/gpx/2.gpx');

        $result = $gpxReader->findCoordNearDateTime($this->photo->getDateTime());

        $this->photo->setLatitude($result['latitude']);
        $this->photo->setLongitude($result['longitude']);
    }
}