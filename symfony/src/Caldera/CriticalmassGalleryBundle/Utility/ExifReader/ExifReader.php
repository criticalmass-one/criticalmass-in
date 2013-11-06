<?php

namespace Caldera\CriticalmassGalleryBundle\Utility\ExifReader;


class ExifReader extends BaseExifReader
{
    public function getMake()
    {
        return $this->exifArray['Make'];
    }

    public function getModel()
    {
        return $this->exifArray['Model'];
    }

    public function getLens()
    {
        foreach ($this->exifArray as $exifArrayValue)
        {

            if (is_string($exifArrayValue) && strpos($exifArrayValue, "USM") > 0)
            {
                return $exifArrayValue;
            }
        }

        return null;
    }

    public function getShutterSpeed()
    {
        return $this->calcFraction($this->exifArray['ExposureTime']);
    }

    public function getGpsCoordinates()
    {
        $egr = new ExifGpsReader();
        $egr->assignExifArray($this->exifArray);
        return $egr->extractCoordinates();
    }

    public function getFlash()
    {
        return $this->exifArray['Flash'];
    }

    public function getExposureBias()
    {
        return $this->calcFraction($this->exifArray['ExposureBiasValue']);
    }

    public function getAperture()
    {
        return $this->calcFraction($this->exifArray['FNumber']);
    }

    public function getIso()
    {
        return $this->exifArray['ISOSpeedRatings'];
    }

    public function getDateTime()
    {
        return $this->exifArray['DateTimeOriginal'];
    }

    public function getFocalLength()
    {
        return $this->calcFraction($this->exifArray['FocalLength']);
    }


} 