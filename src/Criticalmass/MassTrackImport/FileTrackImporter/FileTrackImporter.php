<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\FileTrackImporter;

use App\Criticalmass\UploadFaker\UploadFakerInterface;
use App\Entity\Track;
use App\Entity\TrackImportCandidate;
use App\Event\Track\TrackUploadedEvent;
use Doctrine\Persistence\ManagerRegistry;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Converts a confirmed upload candidate into a Track. The candidate's stored file is
 * already a normalised GPX, so this mirrors the former Strava importer: inject the file
 * via UploadFaker, persist, then dispatch TrackUploadedEvent to run the full enrichment.
 */
class FileTrackImporter implements FileTrackImporterInterface
{
    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly UploadFakerInterface $uploadFaker,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly FilesystemOperator $trackFilesystem,
    ) {
    }

    public function importCandidate(TrackImportCandidate $candidate): Track
    {
        $ride = $candidate->getRide();

        if ($ride === null) {
            throw new \RuntimeException('Cannot import a candidate that is not assigned to a ride.');
        }

        $storagePath = $candidate->getTrackFilename();

        if ($storagePath === null || !$this->trackFilesystem->fileExists($storagePath)) {
            throw new \RuntimeException('The stored candidate file is missing.');
        }

        $user = $candidate->getUser();

        $track = new Track();
        $track
            ->setUser($user)
            ->setUsername($user->getUsername())
            ->setRide($ride)
            ->setSource($this->resolveSource($candidate));

        $this->uploadFaker->fakeUpload($track, 'trackFile', $this->trackFilesystem->read($storagePath), 'upload.gpx');

        $manager = $this->registry->getManager();
        $manager->persist($track);
        $manager->flush();

        $this->eventDispatcher->dispatch(new TrackUploadedEvent($track), TrackUploadedEvent::NAME);

        // The candidate has been consumed — drop it and its stored file.
        $this->trackFilesystem->delete($storagePath);
        $manager->remove($candidate);
        $manager->flush();

        return $track;
    }

    private function resolveSource(TrackImportCandidate $candidate): string
    {
        $originalName = strtolower((string) $candidate->getOriginalName());

        return str_ends_with($originalName, '.fit') ? Track::TRACK_SOURCE_FIT : Track::TRACK_SOURCE_GPX;
    }
}
