<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Image\ExifReader;

use Caldera\Bundle\CriticalmassCoreBundle\Gps\GpsConverter;

class GpsExifReader extends AbstractExifReader
{
    protected $latitude;
    protected $longitude;

    public function hasGpsExifData()
    {
        return (
            $this->exifData &&
            array_key_exists('GPS', $this->exifData) &&
            isset($this->exifData['GPS']['GPSLatitude']) &&
            isset($this->exifData['GPS']['GPSLongitude'])
        );
    }

    public function execute()
    {
        if ($this->hasGpsExifData())
        {
            $gc = new GpsConverter();
            
            $this->latitude = $gc->convert($this->exifData['GPS']['GPSLatitude']);
            $this->longitude = $gc->convert($this->exifData['GPS']['GPSLongitude']);

            $this->photo->setLatitude($this->latitude);
            $this->photo->setLongitude($this->longitude);

            return true;
        }
     
        return false;
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