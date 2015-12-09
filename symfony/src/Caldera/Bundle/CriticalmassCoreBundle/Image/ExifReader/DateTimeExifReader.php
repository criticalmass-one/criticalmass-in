<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Image\ExifReader;

use Symfony\Component\Config\Definition\Exception\Exception;

class DateTimeExifReader extends AbstractExifReader {
    /**
     * @var \DateTime $dateTime
     */
    protected $dateTime = null;

    public function execute()
    {
        if (isset($this->exifData['EXIF']['DateTimeOriginal'])) {
            $this->dateTime = new \DateTime($this->exifData['EXIF']['DateTimeOriginal']);

            //$this->dateTime->setTimezone(new \DateTimeZone('Europe/Berlin'));
        }

        return $this;
    }
    
    public function getDateTime()
    {
        return $this->dateTime;
    }
}