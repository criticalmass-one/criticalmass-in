<?php declare(strict_types=1);

namespace App\EventSubscriber;

use App\Criticalmass\Geocoding\ReverseGeocoderInterface;
use App\Criticalmass\Image\ExifHandler\ExifHandlerInterface;
use App\Criticalmass\Image\GoogleCloud\ExportDataHandler\ExportDataHandlerInterface;
use App\Criticalmass\Image\PhotoGps\PhotoGpsInterface;
use App\Entity\Photo;
use App\Entity\Track;
use App\Event\Photo\PhotoDeletedEvent;
use App\Event\Photo\PhotoUpdatedEvent;
use App\Event\Photo\PhotoUploadedEvent;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PhotoEventSubscriber implements EventSubscriberInterface
{
    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var ReverseGeocoderInterface $reverseGeocoder */
    protected $reverseGeocoder;

    /** @var PhotoGpsInterface $photoGps */
    protected $photoGps;

    /** @var ExifHandlerInterface $exifHandler */
    protected $exifHandler;

    /** @var ExportDataHandlerInterface $exportDataHandler */
    protected $exportDataHandler;

    public function __construct(RegistryInterface $registry, ReverseGeocoderInterface $reverseGeocoder, PhotoGpsInterface $photoGps, ExifHandlerInterface $exifHandler, ExportDataHandlerInterface $exportDataHandler)
    {
        $this->registry = $registry;
        $this->reverseGeocoder = $reverseGeocoder;
        $this->photoGps = $photoGps;
        $this->exifHandler = $exifHandler;
        $this->exportDataHandler = $exportDataHandler;
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
        $this->reverseGeocode($photoUploadedEvent->getPhoto());

        $this->exportDataHandler->calculateForEntity($photoUploadedEvent->getPhoto());

        // and this is to flush our changes to the filesystem
        $this->registry->getManager()->flush();
    }

    public function onPhotoUpdated(PhotoUpdatedEvent $photoUpdatedEvent): void
    {
        $this->handleExifData($photoUpdatedEvent->getPhoto(), $photoUpdatedEvent->getTmpFilename());
        $this->reverseGeocode($photoUpdatedEvent->getPhoto());
        $this->locate($photoUpdatedEvent->getPhoto());

        $this->registry->getManager()->flush();
    }

    public function onPhotoDeleted(PhotoDeletedEvent $photoDeletedEvent): void
    {
    }

    protected function reverseGeocode(Photo $photo): void
    {
        $this->reverseGeocoder->reverseGeocode($photo);
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
