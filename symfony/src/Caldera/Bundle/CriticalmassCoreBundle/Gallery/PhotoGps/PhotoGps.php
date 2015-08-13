<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Gallery\PhotoGps;

use Caldera\Bundle\CriticalmassCoreBundle\Gallery\ExifReader\GpsReader;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\GpxReader\GpxReader;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Photo;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Track;

class PhotoGps {
    protected $track;
    protected $photo;
    protected $exifData;
    protected $controller;
    
    public function __construct($controller)
    {
        $this->controller = $controller;
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
        $filename = $this->controller->get('kernel')->getRootDir() . '/../web/gpx/'.$this->track->getId().'.gpx';
        
        $gpxReader = new GpxReader();
        $gpxReader->loadFile($filename);

        $result = $gpxReader->findCoordNearDateTime($this->photo->getDateTime());

        $this->photo->setLatitude($result['latitude']);
        $this->photo->setLongitude($result['longitude']);
    }
}