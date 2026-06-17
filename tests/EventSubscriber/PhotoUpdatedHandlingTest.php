<?php declare(strict_types=1);

namespace Tests\EventSubscriber;

use App\Criticalmass\Image\ExifHandler\ExifHandlerInterface;
use App\Criticalmass\Image\PhotoGps\PhotoGpsInterface;
use App\Entity\Photo;
use App\Entity\Ride;
use App\Entity\Track;
use App\Entity\User;
use App\Event\Photo\PhotoUpdatedEvent;
use App\EventSubscriber\PhotoEventSubscriber;
use App\Repository\TrackRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * Regression für #1422: onPhotoUpdated() rief `getTmpFilename()` auf, das es auf
 * PhotoUpdatedEvent nicht gibt — jeder Dispatch wäre fatal gescheitert.
 */
final class PhotoUpdatedHandlingTest extends TestCase
{
    public function testOnPhotoUpdatedReadsExifWithoutThrowing(): void
    {
        $photo = new Photo();
        $photo->setRide(new Ride());
        $photo->setUser(new User());

        $trackRepository = $this->createMock(TrackRepository::class);
        $trackRepository->method('findByUserAndRide')->willReturn(null);

        $manager = $this->createMock(ObjectManager::class);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getManager')->willReturn($manager);
        $registry->method('getRepository')->with(Track::class)->willReturn($trackRepository);

        $exifHandler = $this->createMock(ExifHandlerInterface::class);
        // Die EXIF-Daten werden aus der persistierten Datei gelesen (kein tmpFile).
        $exifHandler->expects(self::once())
            ->method('readExifDataFromPhotoFile')
            ->with($photo)
            ->willReturn(null);

        $subscriber = new PhotoEventSubscriber($registry, $this->createMock(PhotoGpsInterface::class), $exifHandler);

        // Darf nicht werfen.
        $subscriber->onPhotoUpdated(new PhotoUpdatedEvent($photo));
    }
}
