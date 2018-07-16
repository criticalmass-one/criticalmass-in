<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoUploader;

use App\Entity\Photo;
use App\Event\Photo\PhotoUploadedEvent;
use DirectoryIterator;
use PHPExif\Reader\Reader;

class PhotoUploader extends AbstractPhotoUploader
{
    public function addFile(string $filename): PhotoUploaderInterface
    {
        $this->createPhotoEntity($filename);

        $this->doctrine->getManager()->flush();

        return $this;
    }

    public function addDirectory(string $directoryName): PhotoUploaderInterface
    {
        $dir = new DirectoryIterator($directoryName);

        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {
                $this->addFile($fileinfo->getPathname());
            }
        }

        $this->doctrine->getManager()->flush();

        return $this;
    }

    public function getAddedPhotoList(): array
    {
        return $this->addedPhotoList;
    }

    protected function calculateDateTime(Photo $photo): Photo
    {
        $photoFilename = sprintf('%s/%s', $this->uploadDestinationPhoto, $photo->getImageName());

        $reader = Reader::factory(Reader::TYPE_NATIVE);
        $exif = $reader->getExifFromFile($photoFilename);

        if ($dateTime = $exif->getCreationDate()) {
            $photo->setDateTime($dateTime);
        }

        return $photo;
    }

    protected function calculateLocation(Photo $photo): PhotoUploader
    {
        $this->photoGps
            ->setPhoto($photo)
            ->setTrack($this->track)
            ->execute();

        return $this;
    }

    protected function createPhotoEntity(string $sourceFilename): Photo
    {
        $photo = new Photo();

        $imageFilename = uniqid() . '.jpg';

        $destinationFilename = sprintf('%s/%s', $this->uploadDestinationPhoto, $imageFilename);

        copy($sourceFilename, $destinationFilename);

        $photo
            ->setImageName($imageFilename)
            ->setUser($this->user)
            ->setRide($this->ride)
            ->setCity($this->ride->getCity());

        $this->calculateDateTime($photo);
        $this->calculateLocation($photo);

        $this->eventDispatcher->dispatch(PhotoUploadedEvent::NAME, new PhotoUploadedEvent($photo));

        $this->doctrine->getManager()->persist($photo);

        $this->addedPhotoList[] = $photo;

        return $photo;
    }
}
