<?php
/**
 * Created by IntelliJ IDEA.
 * User: malte
 * Date: 04.02.15
 * Time: 17:13
 */

namespace Caldera\CriticalmassGalleryBundle\Utility\Gps;

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

    protected function coordinateToDec($coordinate) {
        list($dividend, $divisor) = split('/', $coordinate);
        
        if ($divisor == 0) {
            return 0;
        } else {
            return $dividend / $divisor;
        }
    }
    
    public function execute()
    {
        $exif = exif_read_data($this->filename, 0, true);
        
        if (isset($exif['GPS']['GPSLatitude']) && isset($exif['GPS']['GPSLongitude']))
        {
            $result = array();
            
            $deg = $this->coordinateToDec($exif['GPS']['GPSLatitude'][0]);
            $min = $this->coordinateToDec($exif['GPS']['GPSLatitude'][1]);
            $sec = $this->coordinateToDec($exif['GPS']['GPSLatitude'][2]);

            $result['latitude'] = $deg + ((($min * 60) + ($sec)) / 3600);
            
            $deg = $this->coordinateToDec($exif['GPS']['GPSLongitude'][0]);
            $min = $this->coordinateToDec($exif['GPS']['GPSLongitude'][1]);
            $sec = $this->coordinateToDec($exif['GPS']['GPSLongitude'][2]);

            $result['longitude'] = $deg + ((($min * 60) + ($sec)) / 3600);
            
            return $result;
        }
        else
        {
            return null;
        }
    }
}