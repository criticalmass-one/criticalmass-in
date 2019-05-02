<?php declare(strict_types=1);

namespace App\Criticalmass\Image\ExifHandler;

use App\Entity\Photo;
use PHPExif\Exif;
use PHPExif\Reader\Reader;

class ExifHandler extends AbstractExifHandler
{
    public function readExifDataFromPhotoFile(Photo $photo): ?Exif
    {
        $filename = sprintf('%s/%s', $this->uploadDestinationPhoto, $photo->getImageName());

        $reader = Reader::factory(Reader::TYPE_NATIVE);
        $exif = $reader->read($filename);

        if ($exif !== false) {
            return $exif;
        }

        return null;
    }

    public function assignExifDataToPhoto(Photo $photo, Exif $exif): Photo
    {
        $reflection = new \ReflectionClass($photo);

        /** @var \ReflectionProperty $property */
        foreach ($reflection->getProperties() as $property) {
            if (strpos($property->getName(), 'exif') !== 0) {
                continue;
            }


        }
    }
}