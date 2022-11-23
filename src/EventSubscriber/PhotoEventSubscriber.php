<?php declare(strict_types=1);

namespace App\EventSubscriber;

use App\Criticalmass\Image\ExifHandler\ExifHandlerInterface;
use App\Criticalmass\Image\PhotoGps\PhotoGpsInterface;
use App\Entity\Photo;
use App\Entity\Track;
use App\Event\Photo\PhotoDeletedEvent;
use App\Event\Photo\PhotoUpdatedEvent;
use App\Event\Photo\PhotoUploadedEvent;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PhotoEventSubscriber implements EventSubscriberInterface
{
    protected ManagerRegistry $registry;
    protected PhotoGpsInterface $photoGps;
    protected ExifHandlerInterface $exifHandler;

    public function __construct(ManagerRegistry $registry, PhotoGpsInterface $photoGps, ExifHandlerInterface $exifHandler)
    {
        $this->registry = $registry;
        $this->photoGps = $photoGps;
        $this->exifHandler = $exifHandler;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PhotoUploadedEvent::NAME => 'onPhotoUploaded',
            PhotoUpdatedEvent::NAME => 'onPhotoUpdated',
            PhotoDeletedEvent::NAME => 'onPhotoDeleted',
        ];
    }

    public function onPhotoUploaded(PhotoUploadedEvent $photoUploadedEvent): void
    {
        // this is for persisting the uploaded file into the filesystem
        $this->registry->getManager()->flush();

        $this->handleExifData($photoUploadedEvent->getPhoto(), $photoUploadedEvent->getTmpFilename());
        $this->locate($photoUploadedEvent->getPhoto());

        // and this is to flush our changes to the filesystem
        $this->registry->getManager()->flush();
    }

    public function onPhotoUpdated(PhotoUpdatedEvent $photoUpdatedEvent): void
    {
        $this->handleExifData($photoUpdatedEvent->getPhoto(), $photoUpdatedEvent->getTmpFilename());
        $this->locate($photoUpdatedEvent->getPhoto());

        $this->registry->getManager()->flush();
    }

    public function onPhotoDeleted(PhotoDeletedEvent $photoDeletedEvent): void
    {
    }

    protected function locate(Photo $photo): void
    {
        $track = $this->registry->getRepository(Track::class)->findByUserAndRide($photo->getRide(), $photo->getUser());

        if ($track) {
            try {
                $photo = $this->photoGps
                    ->setPhoto($photo)
                    ->setTrack($track)
                    ->execute()
                    ->getPhoto();
            } catch (\Exception $exception) {

            }
        }
    }

    protected function handleExifData(Photo $photo, string $tmpFilename = null): void
    {
        if ($tmpFilename) {
            $exif = $this->exifHandler->readExifDataFromFile($tmpFilename);
        } else {
            $exif = $this->exifHandler->readExifDataFromPhotoFile($photo);
        }

        if ($exif) {
            $this->exifHandler->assignExifDataToPhoto($photo, $exif);
        }
    }
}
