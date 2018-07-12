<?php declare(strict_types=1);

namespace App\EventSubscriber;

use App\Criticalmass\Geocoding\ReverseGeocoderInterface;
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

    public function __construct(RegistryInterface $registry, ReverseGeocoderInterface $reverseGeocoder)
    {
        $this->registry = $registry;

        $this->reverseGeocoder = $reverseGeocoder;
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
        $this->reverseGeocoder->reverseGeocode($photoUploadedEvent->getPhoto());
    }

    public function onPhotoUpdated(PhotoUpdatedEvent $photoUpdatedEvent): void
    {
        $this->reverseGeocoder->reverseGeocode($photoUpdatedEvent->getPhoto());
    }

    public function onPhotoDeleted(PhotoDeletedEvent $photoDeletedEvent): void
    {
    }
}
