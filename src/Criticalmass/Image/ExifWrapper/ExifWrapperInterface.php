<?php declare(strict_types=1);

namespace App\Criticalmass\Image\ExifWrapper;

use App\Entity\Photo;
use PHPExif\Exif;

interface ExifWrapperInterface
{
    public function getExifData(Photo $photo): ?Exif;
    public function readExifDataFromFile($filename): ?Exif;
}