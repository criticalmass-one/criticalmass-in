<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoUploader;

use App\Entity\Photo;
use App\Event\Photo\PhotoUploadedEvent;
use DirectoryIterator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PhotoUploader extends AbstractPhotoUploader
{
    public function addFile(string $filename): PhotoUploaderInterface
    {
        $this->createPhotoEntity($filename);

        $this->doctrine->getManager()->flush();

        return $this;
    }

    public function addUploadedFile(UploadedFile $uploadedFile): PhotoUploaderInterface
    {
        $this->createUploadedPhotoEntity($uploadedFile);

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
        $exif = $this->exifWrapper->getExifData($photo);

        if ($exif && $dateTime = $exif->getCreationDate()) {
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

    protected function createUploadedPhotoEntity(UploadedFile $uploadedFile): Photo
    {
        $photo = new Photo();

        $photo
            ->setUser($this->user)
            ->setRide($this->ride)
            ->setCity($this->ride->getCity())
            ->setImageFile($uploadedFile);

        $this->calculateDateTime($photo);
        $this->calculateLocation($photo);

        $this->eventDispatcher->dispatch(PhotoUploadedEvent::NAME, new PhotoUploadedEvent($photo));

        $this->doctrine->getManager()->persist($photo);

        $this->addedPhotoList[] = $photo;

        return $photo;
    }

    protected function createPhotoEntity(string $sourceFilename): Photo
    {
        $photo = new Photo();

        $photo
            ->setUser($this->user)
            ->setRide($this->ride)
            ->setCity($this->ride->getCity());

        $this->fakeUpload($photo, file_get_contents($sourceFilename));

        $this->calculateDateTime($photo);
        $this->calculateLocation($photo);

        $this->eventDispatcher->dispatch(PhotoUploadedEvent::NAME, new PhotoUploadedEvent($photo));

        $this->doctrine->getManager()->persist($photo);

        $this->addedPhotoList[] = $photo;

        return $photo;
    }

    protected function fakeUpload(Photo $photo, string $imageContent): Photo
    {
        $filename = sprintf('%s.jpg', uniqid('', true));
        $path = sprintf('/tmp/%s', $filename);

        $filesystem = new Filesystem();
        $filesystem->dumpFile($path, $imageContent);

        $file = new UploadedFile($path, $filename, null, null, true);
        $photo->setImageFile($file);

        return $photo;
    }
}
