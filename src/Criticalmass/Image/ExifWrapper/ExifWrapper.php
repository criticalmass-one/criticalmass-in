<?php declare(strict_types=1);

namespace App\Criticalmass\Image\ExifWrapper;

use App\Entity\Photo;
use PHPExif\Exif;
use PHPExif\Reader\Reader;

class ExifWrapper extends AbstractExifWrapper
{
    public function getExifData(Photo $photo): ?Exif
    {
        $filename = $this->dumpFileToTmp($photo);

        $exif = $this->readExifDataFromFile($filename);

        $this->deleteTmpFile($filename);

        return $exif;
    }

    protected function dumpFileToTmp(Photo $photo): string
    {
        $path = sprintf('/tmp/%s', uniqid('', true));

        $imageContent = $this->flysystemFilesystem->read($photo->getImageName());

        $this->symfonyFilesystem->dumpFile($path, $imageContent);

        return $path;
    }

    public function readExifDataFromFile($filename): ?Exif
    {
        $reader = Reader::factory(Reader::TYPE_NATIVE);
        $exif = $reader->read($filename);

        if ($exif !== false) {
            return $exif;
        }

        return null;
    }

    protected function deleteTmpFile($filename): ExifWrapper
    {
        $this->symfonyFilesystem->remove($filename);

        return $this;
    }
}