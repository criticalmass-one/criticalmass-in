<?php declare(strict_types=1);

namespace Tests\EventSubscriber;

use App\Criticalmass\Image\ExifHandler\ExifHandlerInterface;
use App\Criticalmass\Image\PhotoGps\PhotoGpsInterface;
use App\Entity\Photo;
use App\Entity\Ride;
use App\Entity\Track;
use App\Entity\User;
use App\Event\Photo\PhotoUploadedEvent;
use App\EventSubscriber\PhotoEventSubscriber;
use App\Repository\TrackRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Stellt sicher, dass eine fehlgeschlagene Foto-Geolokalisierung den Upload
 * nicht abbricht, aber nicht mehr stillschweigend verschluckt wird (#1400).
 */
final class PhotoEventSubscriberTest extends TestCase
{
    public function testFailedGeolocationIsLoggedAndDoesNotThrow(): void
    {
        $photo = new Photo();
        $photo->setRide(new Ride());
        $photo->setUser(new User());

        $trackRepository = $this->createMock(TrackRepository::class);
        $trackRepository->method('findByUserAndRide')->willReturn(new Track());

        $manager = $this->createMock(ObjectManager::class);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getManager')->willReturn($manager);
        $registry->method('getRepository')->with(Track::class)->willReturn($trackRepository);

        $exifHandler = $this->createMock(ExifHandlerInterface::class);
        $exifHandler->method('readExifDataFromPhotoFile')->willReturn(null);

        $photoGps = $this->createMock(PhotoGpsInterface::class);
        $photoGps->method('setPhoto')->willReturnSelf();
        $photoGps->method('setTrack')->willReturnSelf();
        $photoGps->method('execute')->willThrowException(new \RuntimeException('GPS service down'));

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())
            ->method('error')
            ->with(self::stringContains('geolocation failed'), self::anything());

        $subscriber = new PhotoEventSubscriber($registry, $photoGps, $exifHandler, $logger);

        // Darf nicht werfen.
        $subscriber->onPhotoUploaded(new PhotoUploadedEvent($photo));
    }
}
