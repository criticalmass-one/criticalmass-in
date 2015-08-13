<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Gallery\ExifReader;

class DateTimeReader extends AbstractExifReader {
    protected $dateTime;

    public function execute()
    {
        if (isset($this->exifData['IFD0']['DateTime']))
        {
            $dateTime = $this->exifData['IFD0']['DateTime'];

            $dateTimeParts = explode(' ', $dateTime);

            $dateTimeParts[0] = str_replace(':', '-', $dateTimeParts[0]);

            $dateTime = implode(' ', $dateTimeParts);
            
            $this->dateTime = new \DateTime($dateTime);
        }
        else
        {
            return null;
        }
    }
    
    public function getDateTime()
    {
        return $this->dateTime;
    }
}