<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoUploader;

use App\Entity\Photo;
use App\Event\Photo\PhotoUploadedEvent;
use DirectoryIterator;
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

    protected function createUploadedPhotoEntity(UploadedFile $uploadedFile): Photo
    {
        $photo = new Photo();

        $photo
            ->setUser($this->user)
            ->setRide($this->ride)
            ->setCity($this->ride->getCity())
            ->setImageFile($uploadedFile);

        $this->doctrine->getManager()->persist($photo);

        $this->eventDispatcher->dispatch(new PhotoUploadedEvent($photo, true, $uploadedFile->getRealPath()), PhotoUploadedEvent::NAME);

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

        $tmpFilename = $this->uploadFaker->fakeUpload($photo, 'imageFile', file_get_contents($sourceFilename), $this->extractFilename($sourceFilename));

        $this->doctrine->getManager()->persist($photo);
        
        $this->eventDispatcher->dispatch(new PhotoUploadedEvent($photo, true, $tmpFilename), PhotoUploadedEvent::NAME);

        $this->addedPhotoList[] = $photo;

        return $photo;
    }

    protected function extractFilename(string $sourceFilename): string
    {
        $filenameParts = explode('/', $sourceFilename);

        return array_shift($filenameParts);
    }
}
