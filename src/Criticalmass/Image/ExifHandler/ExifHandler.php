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

        return $this->readExifDataFromFile($filename);
    }

    public function readExifDataFromFile(string $filename): ?Exif
    {
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

        /** @var \ReflectionMethod $method */
        foreach ($reflection->getMethods() as $method) {
            if (strpos($method->getName(), 'setExif') !== 0) {
                continue;
            }

            $exifProperty = substr($method->getName(), 7);
            $exifGetMethodName = sprintf('get%s', $exifProperty);
            $photoSetMethodName = $method->getName();

            $type = $method->getParameters()[0]->getType();

            $this->typeawareAssignment($photo, $photoSetMethodName, $exif, $exifGetMethodName, $type->getName());
        }

        dump($photo);

        return $photo;
    }

    protected function typeawareAssignment(Photo $photo, string $setMethodName, Exif $exif, string $getMethodName, string $type): Photo
    {
        switch ($type) {
            case 'string': $photo->$setMethodName((string) $exif->$getMethodName());
                break;
            case 'int': $photo->$setMethodName((int) $exif->$getMethodName());
                break;
            case 'float': $photo->$setMethodName((float) $exif->$getMethodName());
                break;
        }

        return $photo;
    }
}