<?php declare(strict_types=1);

namespace App\EventSubscriber;

use App\Criticalmass\Geocoding\ReverseGeocoderInterface;
use App\Criticalmass\Image\ExifWrapper\ExifWrapperInterface;
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

    /** @var ExifWrapperInterface $exifWrapper */
    protected $exifWrapper;

    public function __construct(RegistryInterface $registry, ReverseGeocoderInterface $reverseGeocoder, PhotoGpsInterface $photoGps, ExifWrapperInterface $exifWrapper)
    {
        $this->registry = $registry;
        $this->reverseGeocoder = $reverseGeocoder;
        $this->photoGps = $photoGps;
        $this->exifWrapper = $exifWrapper;
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
        $this->calculateDateTime($photoUploadedEvent->getPhoto());
        $this->reverseGeocode($photoUploadedEvent->getPhoto());
        $this->locate($photoUploadedEvent->getPhoto());
    }

    public function onPhotoUpdated(PhotoUpdatedEvent $photoUpdatedEvent): void
    {
        $this->calculateDateTime($photoUpdatedEvent->getPhoto());
        $this->reverseGeocode($photoUpdatedEvent->getPhoto());
        $this->locate($photoUpdatedEvent->getPhoto());
    }

    public function onPhotoDeleted(PhotoDeletedEvent $photoDeletedEvent): void
    {
    }

    protected function calculateDateTime(Photo $photo): void
    {
        $exif = $this->exifWrapper->getExifData($photo);

        if ($exif && $dateTime = $exif->getCreationDate()) {
            $photo->setDateTime($dateTime);
        }
    }

    protected function reverseGeocode(Photo $photo): void
    {
        $this->reverseGeocoder->reverseGeocode($photo);
    }

    protected function locate(Photo $photo, bool $flush = true): void
    {
        $track = $this->registry->getRepository(Track::class)->findByUserAndRide($photo->getRide(), $photo->getUser());

        if ($track) {
            try {
                $photo = $this->photoGps
                    ->setPhoto($photo)
                    ->setTrack($track)
                    ->execute()
                    ->getPhoto();

                if ($flush) {
                    $this->registry->getManager()->flush();
                }
            } catch (\Exception $exception) {

            }
        }
    }
}
