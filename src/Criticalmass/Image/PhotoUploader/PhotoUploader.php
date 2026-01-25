<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoUploader;

use App\Criticalmass\UploadFaker\UploadFakerInterface;
use App\Entity\Photo;
use App\Entity\Ride;
use App\Entity\Track;
use App\Entity\User;
use App\Event\Photo\PhotoUploadedEvent;
use DirectoryIterator;
use Doctrine\Persistence\ManagerRegistry;
use League\Flysystem\Filesystem;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PhotoUploader implements PhotoUploaderInterface
{
    private User $user;
    private Ride $ride;
    private array $addedPhotoList = [];

    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly UploadFakerInterface $uploadFaker
    )
    {

    }

    public function setUser(User $user): PhotoUploaderInterface
    {
        $this->user = $user;

        return $this;
    }

    public function setRide(Ride $ride): PhotoUploaderInterface
    {
        $this->ride = $ride;

        return $this;
    }

    /** @deprecated  */
    public function setTrack(?Track $track = null): PhotoUploaderInterface
    {
        return $this;
    }

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
