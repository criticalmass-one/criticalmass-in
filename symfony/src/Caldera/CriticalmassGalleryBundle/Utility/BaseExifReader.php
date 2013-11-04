<?php
/**
 * Created by PhpStorm.
 * User: Malte
 * Date: 02.11.13
 * Time: 12:02
 */

namespace Caldera\CriticalmassGalleryBundle\Utility;


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

        if (count($tmpArray) == 1)
        {
            return $tmpArray['0']/$tmpArray['1'];
        }

        return $valueString;
    }
} 