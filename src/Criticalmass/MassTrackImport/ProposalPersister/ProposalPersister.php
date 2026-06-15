<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\ProposalPersister;

use App\Criticalmass\MassTrackImport\TrackDecider\RideResult;
use App\Entity\Track;
use App\Entity\TrackImportCandidate;
use Doctrine\Persistence\ManagerRegistry;

class ProposalPersister implements ProposalPersisterInterface
{
    public function __construct(private readonly ManagerRegistry $registry)
    {
    }

    public function persist(RideResult $rideResult): RideResult
    {
        $candidate = $rideResult->getActivity();
        $manager = $this->registry->getManager();

        if (!$this->isDuplicate($candidate)) {
            $manager->persist($candidate);
        }

        $manager->flush();

        return $rideResult;
    }

    private function isDuplicate(TrackImportCandidate $candidate): bool
    {
        $user = $candidate->getUser();
        $candidateRepository = $this->registry->getRepository(TrackImportCandidate::class);

        // Gezielte Existenz-Abfrage statt die gesamte Track-/Candidate-Collection
        // des Users zu hydratisieren und per in_array zu prüfen.
        if ($candidate->getSource() === TrackImportCandidate::CANDIDATE_SOURCE_UPLOAD) {
            $fileHash = $candidate->getFileHash();

            return $fileHash !== null && null !== $candidateRepository->findOneBy([
                'user' => $user,
                'source' => TrackImportCandidate::CANDIDATE_SOURCE_UPLOAD,
                'fileHash' => $fileHash,
            ]);
        }

        $activityId = $candidate->getActivityId();

        if ($activityId === null) {
            return false;
        }

        // 'stravaActitityId' ist der (verschriebene) Property-/Feldname in Track.
        return null !== $this->registry->getRepository(Track::class)->findOneBy([
            'user' => $user,
            'stravaActitityId' => $activityId,
        ]) || null !== $candidateRepository->findOneBy([
            'user' => $user,
            'activityId' => $activityId,
        ]);
    }
}
