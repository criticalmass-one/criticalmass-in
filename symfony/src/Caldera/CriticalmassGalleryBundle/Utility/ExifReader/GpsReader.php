<?php

namespace Caldera\CriticalmassGalleryBundle\Utility\ExifReader;

use Caldera\CriticalmassGalleryBundle\Utility\Gps\GpsConverter;

class GpsReader extends AbstractExifReader {
    protected $latitude;
    protected $longitude;
    
    public function execute()
    {
        if (isset($this->exifData['GPS']['GPSLatitude']) && isset($this->exifData['GPS']['GPSLongitude']))
        {
            $gc = new GpsConverter();
            
            $result = array();

            $this->latitude = $gc->convert($this->exifData['GPS']['GPSLatitude']);
            $this->longitude = $gc->convert($this->exifData['GPS']['GPSLongitude']);
            
            return $result;
        }
        else
        {
            return null;
        }
    }
    
    public function getLatitude()
    {
        return $this->latitude;
    }
    
    public function getLongitude()
    {
        return $this->longitude;
    }
}