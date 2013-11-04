<?php
/**
 * Created by PhpStorm.
 * User: Malte
 * Date: 01.11.13
 * Time: 21:11
 */

namespace Caldera\CriticalmassGalleryBundle\Utility;


class ExifGpsReader extends BaseExifReader
{
    public function extractCoordinates()
    {
        $latitude = $this->calcFraction($this->exifArray['GPSLatitude']['0']) +
                    $this->calcFraction($this->exifArray['GPSLatitude']['1'])/60 +
                    $this->calcFraction($this->exifArray['GPSLatitude']['2'])/3600;

        $longitude = $this->calcFraction($this->exifArray['GPSLongitude']['0']) +
                     $this->calcFraction($this->exifArray['GPSLongitude']['1'])/60 +
                     $this->calcFraction($this->exifArray['GPSLongitude']['2'])/3600;

        return array(
            "latitude" => $latitude,
            "longitude" => $longitude
        );
    }
} 