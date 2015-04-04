<?php

namespace Caldera\CriticalmassGalleryBundle\Utility\ExifReader;

use Caldera\CriticalmassGalleryBundle\Entity\Photo;

class GpsReader {
    protected $filename;

    public function setPhoto(Photo $photo)
    {
        $this->filename = $photo->getFilePath();
    }

    public function setFilename($filename)
    {
        $this->filename = $filename;
    }
    
    public function execute()
    {
        $exif = exif_read_data($this->filename, 0, true);
        
        if (isset($exif['GPS']['GPSLatitude']) && isset($exif['GPS']['GPSLongitude']))
        {
            $gc = new GpsConverter();
            
            $result = array();

            $result['latitude'] = $gc->convert($exif['GPS']['GPSLatitude']);
            $result['longitude'] = $gc->convert($exif['GPS']['GPSLongitude']);
            
            return $result;
        }
        else
        {
            return null;
        }
    }
}