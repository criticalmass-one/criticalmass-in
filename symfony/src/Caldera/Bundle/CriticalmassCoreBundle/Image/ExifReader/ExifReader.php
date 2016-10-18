<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Image\ExifReader;

class ExifReader extends AbstractExifReader
{
    /**
     * @var ExifData $exifData
     */
    protected $exifData;

    public function execute()
    {
        $this->exifData = new ExifData();

        $this->exifData
            ->setShutterSpeed($this->exif['EXIF']['ExposureTime'])
            ->setPhotoDateTime(new \DateTime($this->exif['EXIF']['DateTimeOriginal']))
            ->setFocalLength($this->calculateFocalLength())
            ->setAperture($this->calculateAperture())
            ->setModel($this->exif['IFD0']['Model'])
            ->setFlash($this->calculateFlash())
            ->setLens($this->exif['EXIF']['UndefinedTag:0xA434']);

        return $this->exifData;
    }

    protected function calculateFocalLength()
    {
        $focalLengthParts = explode('/', $this->exif['EXIF']['FocalLength']);

        $focalLength = $focalLengthParts[0] / $focalLengthParts[1];

        return $focalLength;
    }

    protected function calculateAperture()
    {
        $apertureParts = explode('/', $this->exif['EXIF']['FNumber']);

        $aperture = $apertureParts[0] / $apertureParts[1];

        $aperture = number_format($aperture, 1);

        return $aperture;
    }

    protected function calculateFlash()
    {
        return $this->exif['EXIF']['Flash'] != 16;
    }
}