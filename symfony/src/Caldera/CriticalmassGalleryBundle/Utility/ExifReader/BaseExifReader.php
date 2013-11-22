<?php

namespace Caldera\CriticalmassGalleryBundle\Utility\ExifReader;

abstract class BaseExifReader {

    protected $exifArray = array();

    public function assignImageFile($filename)
    {
        $this->assignExifArray(exif_read_data($filename));
    }

    public function assignExifArray($array)
    {
        $this->exifArray = $array;
    }

    protected function calcFraction($valueString)
    {
        $tmpArray = explode("/", $valueString);

        if (count($tmpArray) == 2)
        {
            return $tmpArray['0']/$tmpArray['1'];
        }

        return $valueString;
    }
} 