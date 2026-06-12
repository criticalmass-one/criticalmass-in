<?php declare(strict_types=1);

namespace Tests\Criticalmass\MassTrackImport\FileTrackImporter;

use App\Criticalmass\MassTrackImport\FileTrackImporter\FileTrackImporter;
use App\Criticalmass\UploadFaker\UploadFakerInterface;
use App\Entity\Ride;
use App\Entity\Track;
use App\Entity\TrackImportCandidate;
use App\Entity\User;
use App\Event\Track\TrackUploadedEvent;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use League\Flysystem\FilesystemOperator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FileTrackImporterTest extends TestCase
{
    public function testImportsGpxCandidateIntoTrackAndCleansUp(): void
    {
        $candidate = $this->candidate('my-ride.gpx', 'candidates/abc.gpx');

        $filesystem = $this->createMock(FilesystemOperator::class);
        $filesystem->method('fileExists')->willReturn(true);
        $filesystem->method('read')->willReturn('<gpx/>');
        $filesystem->expects(self::once())->method('delete')->with('candidates/abc.gpx');

        $uploadFaker = $this->createMock(UploadFakerInterface::class);
        $uploadFaker->expects(self::once())->method('fakeUpload')
            ->with(self::isInstanceOf(Track::class), 'trackFile', '<gpx/>', 'upload.gpx');

        $manager = $this->createMock(ObjectManager::class);
        $manager->expects(self::once())->method('persist')->with(self::isInstanceOf(Track::class));
        $manager->expects(self::once())->method('remove')->with($candidate);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects(self::once())->method('dispatch')
            ->with(self::isInstanceOf(TrackUploadedEvent::class), TrackUploadedEvent::NAME)
            ->willReturnArgument(0);

        $track = $this->importer($manager, $uploadFaker, $dispatcher, $filesystem)->importCandidate($candidate);

        self::assertSame(Track::TRACK_SOURCE_GPX, $track->getSource());
        self::assertSame('tester', $track->getUsername());
    }

    public function testFitOriginalNameYieldsFitSource(): void
    {
        $candidate = $this->candidate('garmin-activity.fit', 'candidates/def.gpx');

        $filesystem = $this->createMock(FilesystemOperator::class);
        $filesystem->method('fileExists')->willReturn(true);
        $filesystem->method('read')->willReturn('<gpx/>');

        $track = $this->importer(
            $this->createMock(ObjectManager::class),
            $this->createMock(UploadFakerInterface::class),
            $this->dispatcherReturningEvent(),
            $filesystem,
        )->importCandidate($candidate);

        self::assertSame(Track::TRACK_SOURCE_FIT, $track->getSource());
    }

    public function testCandidateWithoutRideThrows(): void
    {
        $candidate = (new TrackImportCandidate())->setTrackFilename('candidates/x.gpx');

        $this->expectException(\RuntimeException::class);

        $this->importer(
            $this->createMock(ObjectManager::class),
            $this->createMock(UploadFakerInterface::class),
            $this->createMock(EventDispatcherInterface::class),
            $this->createMock(FilesystemOperator::class),
        )->importCandidate($candidate);
    }

    private function candidate(string $originalName, string $storagePath): TrackImportCandidate
    {
        $user = $this->createMock(User::class);
        $user->method('getUsername')->willReturn('tester');

        return (new TrackImportCandidate())
            ->setUser($user)
            ->setRide($this->createMock(Ride::class))
            ->setOriginalName($originalName)
            ->setTrackFilename($storagePath);
    }

    private function dispatcherReturningEvent(): EventDispatcherInterface
    {
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->method('dispatch')->willReturnArgument(0);

        return $dispatcher;
    }

    private function importer(
        ObjectManager $manager,
        UploadFakerInterface $uploadFaker,
        EventDispatcherInterface $dispatcher,
        FilesystemOperator $filesystem,
    ): FileTrackImporter {
        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getManager')->willReturn($manager);

        return new FileTrackImporter($registry, $uploadFaker, $dispatcher, $filesystem);
    }
}
