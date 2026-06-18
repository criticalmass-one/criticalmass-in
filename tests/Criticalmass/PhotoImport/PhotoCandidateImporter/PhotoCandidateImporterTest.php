<?php declare(strict_types=1);

namespace Tests\Criticalmass\PhotoImport\PhotoCandidateImporter;

use App\Criticalmass\PhotoImport\PhotoCandidateImporter\PhotoCandidateImporter;
use App\Criticalmass\UploadFaker\UploadFakerInterface;
use App\Entity\City;
use App\Entity\Photo;
use App\Entity\PhotoImportCandidate;
use App\Entity\Ride;
use App\Entity\User;
use App\Event\Photo\PhotoUploadedEvent;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use League\Flysystem\FilesystemOperator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PhotoCandidateImporterTest extends TestCase
{
    public function testImportsGalleryIntoPhotosAndCleansUp(): void
    {
        $candidate = $this->candidate('IMG_4711.heic', 'a1b2.jpg');
        $ride = $this->ride();

        $filesystem = $this->createMock(FilesystemOperator::class);
        $filesystem->method('fileExists')->willReturn(true);
        $filesystem->method('read')->willReturn('JPEGBYTES');
        $filesystem->expects(self::once())->method('delete')->with('a1b2.jpg');

        $uploadFaker = $this->createMock(UploadFakerInterface::class);
        // HEIC original is staged as JPEG, so the faked upload carries the .jpg name.
        $uploadFaker->expects(self::once())->method('fakeUpload')
            ->with(self::isInstanceOf(Photo::class), 'imageFile', 'JPEGBYTES', 'IMG_4711.jpg')
            ->willReturn('/tmp/whatever');

        $manager = $this->createMock(ObjectManager::class);
        $manager->expects(self::once())->method('persist')->with(self::isInstanceOf(Photo::class));
        $manager->expects(self::once())->method('remove')->with($candidate);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects(self::once())->method('dispatch')
            ->with(self::isInstanceOf(PhotoUploadedEvent::class), PhotoUploadedEvent::NAME)
            ->willReturnArgument(0);

        $photos = $this->importer($manager, $uploadFaker, $dispatcher, $filesystem)->importGallery([$candidate], $ride);

        self::assertCount(1, $photos);
        self::assertSame($ride, $photos[0]->getRide());
        self::assertSame($ride->getCity(), $photos[0]->getCity());
    }

    public function testCandidateWithMissingStagedFileIsSkipped(): void
    {
        $candidate = $this->candidate('photo.jpg', 'gone.jpg');

        $filesystem = $this->createMock(FilesystemOperator::class);
        $filesystem->method('fileExists')->willReturn(false);
        $filesystem->expects(self::never())->method('read');
        $filesystem->expects(self::never())->method('delete');

        $manager = $this->createMock(ObjectManager::class);
        $manager->expects(self::never())->method('persist');
        $manager->expects(self::never())->method('remove');

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects(self::never())->method('dispatch');

        $photos = $this->importer($manager, $this->createMock(UploadFakerInterface::class), $dispatcher, $filesystem)
            ->importGallery([$candidate], $this->ride());

        self::assertSame([], $photos);
    }

    private function candidate(string $originalName, string $stagedFilename): PhotoImportCandidate
    {
        return (new PhotoImportCandidate())
            ->setUser($this->createMock(User::class))
            ->setFileHash('a1b2')
            ->setStagedFilename($stagedFilename)
            ->setOriginalName($originalName);
    }

    private function ride(): Ride
    {
        $ride = $this->createMock(Ride::class);
        $ride->method('getCity')->willReturn($this->createMock(City::class));

        return $ride;
    }

    private function importer(
        ObjectManager $manager,
        UploadFakerInterface $uploadFaker,
        EventDispatcherInterface $dispatcher,
        FilesystemOperator $filesystem,
    ): PhotoCandidateImporter {
        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getManager')->willReturn($manager);

        return new PhotoCandidateImporter($registry, $uploadFaker, $dispatcher, $filesystem);
    }
}
