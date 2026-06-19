<?php declare(strict_types=1);

namespace App\Criticalmass\Upload\Handler;

use App\Criticalmass\MassTrackImport\ProposalPersister\ProposalPersisterInterface;
use App\Criticalmass\MassTrackImport\TrackDecider\TrackDeciderInterface;
use App\Criticalmass\MassTrackImport\UploadedTrackCandidate\UploadedTrackCandidateFactory;
use App\Criticalmass\Upload\UploadResult;
use App\Entity\TrackImportCandidate;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use League\Flysystem\FilesystemOperator;

/**
 * Stages a single uploaded GPX/FIT file: parse it into a candidate, store the
 * normalised GPX, then either match it to a ride or park it for manual review.
 *
 * This is the orchestration the bulk track upload used to perform inline; it now
 * lives behind a handler so the unified upload dispatcher can reuse it unchanged.
 */
class TrackUploadHandler
{
    private const CANDIDATE_DIRECTORY = 'candidates';

    public function __construct(
        private readonly UploadedTrackCandidateFactory $candidateFactory,
        private readonly TrackDeciderInterface $trackDecider,
        private readonly ProposalPersisterInterface $proposalPersister,
        private readonly FilesystemOperator $trackFilesystem,
        private readonly ManagerRegistry $registry,
    ) {
    }

    /**
     * @throws \RuntimeException if the file cannot be parsed into a usable track
     */
    public function handle(string $filePath, string $originalName, User $user): UploadResult
    {
        $parsed = $this->candidateFactory->createFromUpload($filePath, $originalName, $user);

        $candidate = $parsed->getCandidate();
        $fileHash = (string) $candidate->getFileHash();

        if ($this->candidateAlreadyExists($user, $fileHash)) {
            return new UploadResult(UploadResult::KIND_TRACK, UploadResult::STATUS_DUPLICATE, 'Diese Datei hast du bereits hochgeladen.');
        }

        $storagePath = sprintf('%s/%s.gpx', self::CANDIDATE_DIRECTORY, $fileHash);
        $this->trackFilesystem->write($storagePath, $parsed->getGpxXml());
        $candidate->setTrackFilename($storagePath);

        $rideResult = $this->trackDecider->decide($candidate);

        if ($rideResult !== null) {
            $this->proposalPersister->persist($rideResult);

            return new UploadResult(UploadResult::KIND_TRACK, UploadResult::STATUS_MATCHED, sprintf('Der Tour „%s“ zugeordnet.', $rideResult->getRide()->getTitle()));
        }

        // No confident ride match → park the candidate without a ride for manual review.
        // (The decider may have pre-set a below-threshold ride, so reset it explicitly.)
        $candidate->setRide(null);

        $manager = $this->registry->getManager();
        $manager->persist($candidate);
        $manager->flush();

        return new UploadResult(UploadResult::KIND_TRACK, UploadResult::STATUS_PARKED, 'Keine passende Tour gefunden — die Datei wurde zur manuellen Prüfung gespeichert.');
    }

    private function candidateAlreadyExists(User $user, string $fileHash): bool
    {
        return null !== $this->registry->getRepository(TrackImportCandidate::class)->findOneBy([
            'user' => $user,
            'fileHash' => $fileHash,
        ]);
    }
}
