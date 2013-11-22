<?php

namespace Caldera\CriticalmassGalleryBundle\Utility\ExifReader;

class ExifGpsReader extends BaseExifReader
{
    public function extractCoordinates()
    {
        $latitude = $this->calcFraction($this->exifArray['GPSLatitude']['0']) +
                    $this->calcFraction($this->exifArray['GPSLatitude']['1'])/60.0 +
                    $this->calcFraction($this->exifArray['GPSLatitude']['2'])/3600.0;

        $longitude = $this->calcFraction($this->exifArray['GPSLongitude']['0']) +
                     $this->calcFraction($this->exifArray['GPSLongitude']['1'])/60.0 +
                     $this->calcFraction($this->exifArray['GPSLongitude']['2'])/3600.0;

        return array(
            "latitude" => $latitude,
            "longitude" => $longitude
        );
    }
} 