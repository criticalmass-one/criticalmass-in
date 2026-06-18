<?php declare(strict_types=1);

namespace App\Criticalmass\PhotoImport\PhotoCandidateImporter;

use App\Criticalmass\UploadFaker\UploadFakerInterface;
use App\Entity\Photo;
use App\Entity\PhotoImportCandidate;
use App\Entity\Ride;
use App\Event\Photo\PhotoUploadedEvent;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Converts a confirmed gallery of photo candidates into Photos. Each candidate's
 * staged file (already a normalised, EXIF-carrying image) is injected via
 * UploadFaker and the PhotoUploadedEvent runs the usual enrichment — EXIF date and
 * GPS/track geolocation — exactly as a regular photo upload would. This mirrors
 * FileTrackImporter on the track side.
 */
class PhotoCandidateImporter implements PhotoCandidateImporterInterface
{
    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly UploadFakerInterface $uploadFaker,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly FilesystemOperator $photoCandidateFilesystem,
    ) {
    }

    public function importGallery(array $candidates, Ride $ride): array
    {
        $manager = $this->registry->getManager();

        $photos = [];

        foreach ($candidates as $candidate) {
            $photo = $this->importCandidate($candidate, $ride, $manager);

            if ($photo !== null) {
                $photos[] = $photo;
            }
        }

        return $photos;
    }

    private function importCandidate(PhotoImportCandidate $candidate, Ride $ride, ObjectManager $manager): ?Photo
    {
        $storagePath = $candidate->getStagedFilename();

        if ($storagePath === null || !$this->photoCandidateFilesystem->fileExists($storagePath)) {
            // The staged file is gone (e.g. cleaned up out of band) — nothing to import.
            return null;
        }

        $photo = new Photo();
        $photo
            ->setUser($candidate->getUser())
            ->setRide($ride)
            ->setCity($ride->getCity());

        $tmpFilename = $this->uploadFaker->fakeUpload(
            $photo,
            'imageFile',
            $this->photoCandidateFilesystem->read($storagePath),
            $this->uploadName($candidate),
        );

        $manager->persist($photo);

        // flush=true plus the temp filename lets the subscriber read EXIF from the
        // freshly injected file and geolocate against the ride's track.
        $this->eventDispatcher->dispatch(new PhotoUploadedEvent($photo, true, $tmpFilename), PhotoUploadedEvent::NAME);

        // The candidate has been consumed — drop it and its staged file.
        $this->photoCandidateFilesystem->delete($storagePath);
        $manager->remove($candidate);
        $manager->flush();

        return $photo;
    }

    /**
     * A client filename carrying the *normalised* extension, so Vich stores the
     * file under the format it actually contains (HEIC originals are JPEG by now).
     */
    private function uploadName(PhotoImportCandidate $candidate): string
    {
        $extension = pathinfo((string) $candidate->getStagedFilename(), PATHINFO_EXTENSION);
        $baseName = pathinfo((string) $candidate->getOriginalName(), PATHINFO_FILENAME);

        if ($baseName === '') {
            $baseName = (string) $candidate->getFileHash();
        }

        return sprintf('%s.%s', $baseName, $extension);
    }
}
