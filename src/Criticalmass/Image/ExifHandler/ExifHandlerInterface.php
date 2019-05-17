<?php declare(strict_types=1);

namespace App\Criticalmass\Image\ExifHandler;

use App\Entity\Photo;
use PHPExif\Exif;

interface ExifHandlerInterface
{
    public function readExifDataFromPhotoFile(Photo $photo): ?Exif;
    public function readExifDataFromFile(string $filename): ?Exif;
    public function assignExifDataToPhoto(Photo $photo, Exif $exif): Photo;
}