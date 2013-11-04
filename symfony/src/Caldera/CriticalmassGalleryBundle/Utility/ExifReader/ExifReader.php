<?php
/**
 * Created by PhpStorm.
 * User: Malte
 * Date: 02.11.13
 * Time: 11:59
 */

namespace Caldera\CriticalmassGalleryBundle\Utility;


class ExifReader extends BaseExifReader
{
    public function getMake()
    {
        return $this->exifArray['ExposureTime'];
    }

    public function getModel()
    {
        return $this->exifArray['ExposureTime'];
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
        return $this->exifArray['ExposureTime'];
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
        return $this->exifArray['ExposureBiasValue'];
    }

    public function getAperture()
    {
        return $this->exifArray['FNumber'];
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
        return $this->exifArray['FocalLength'];
    }


} 