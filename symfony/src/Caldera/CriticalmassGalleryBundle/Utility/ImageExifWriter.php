<?php

namespace Caldera\CriticalmassGalleryBundle\Utility;

use Caldera\CriticalmassGalleryBundle\Entity\Image;
use Caldera\CriticalmassGalleryBundle\Utility\ExifReader\ExifReader;

class ImageExifWriter {
    private $image;

    public function __construct(Image $image)
    {
        $this->image = $image;
    }

    public function readExifFromFilename($filename)
    {
        $exifReader = new ExifReader();
        $exifReader->assignImageFile($filename);

        $this->image->setExifMake($exifReader->getMake());
        $this->image->setExifModel($exifReader->getModel());
        //$this->image->setExifLens($exifReader->getLens());
        $this->image->setExifShutterSpeed($exifReader->getShutterSpeed());
        $this->image->setExifAperture($exifReader->getAperture());
        $this->image->setExifExposureBias($exifReader->getExposureBias());
        $this->image->setExifFocalLength($exifReader->getFocalLength());
        $this->image->setExifIso($exifReader->getIso());
        $this->image->setExifDateTime($exifReader->getDateTime());
        $this->image->setExifFlash($exifReader->getFlash());

        $gpsCoordinates = $exifReader->getGpsCoordinates();
        $this->image->setExifLatitude($gpsCoordinates['latitude']);
        $this->image->setExifLongitude($gpsCoordinates['longitude']);

        return $this->image;
    }
} 