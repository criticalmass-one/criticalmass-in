<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Image\ExifReader;

/**
 * Class DateTimeExifReader
 * @package Caldera\Bundle\CriticalmassCoreBundle\Image\ExifReader
 * @deprecated
 */
class DateTimeExifReader extends AbstractExifReader {
    /**
     * @var \DateTime $dateTime
     */
    protected $dateTime = null;

    public function execute()
    {
        if (isset($this->exif['EXIF']['DateTimeOriginal'])) {
            $this->dateTime = new \DateTime($this->exif['EXIF']['DateTimeOriginal']);

            //$this->dateTime->setTimezone(new \DateTimeZone('Europe/Berlin'));
        }

        return $this;
    }
    
    public function getDateTime()
    {
        return $this->dateTime;
    }
}