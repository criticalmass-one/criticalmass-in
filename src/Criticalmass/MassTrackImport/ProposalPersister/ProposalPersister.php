<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\ProposalPersister;

use App\Criticalmass\MassTrackImport\TrackDecider\RideResult;
use App\Entity\Track;
use App\Entity\TrackImportCandidate;
use App\Entity\User;
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

        if ($candidate->getSource() === TrackImportCandidate::CANDIDATE_SOURCE_UPLOAD) {
            $fileHash = $candidate->getFileHash();

            return $fileHash !== null && in_array($fileHash, $this->loadExistingFileHashes($user), true);
        }

        return in_array($candidate->getActivityId(), $this->loadExistingStravaActivityIds($user), true);
    }

    /**
     * @return list<int>
     */
    private function loadExistingStravaActivityIds(User $user): array
    {
        $activityIds = [];

        /** @var Track $track */
        foreach ($this->registry->getRepository(Track::class)->findBy(['user' => $user]) as $track) {
            if ($track->getStravaActivityId()) {
                $activityIds[] = (int) $track->getStravaActivityId();
            }
        }

        /** @var TrackImportCandidate $candidate */
        foreach ($this->registry->getRepository(TrackImportCandidate::class)->findBy(['user' => $user]) as $candidate) {
            if ($candidate->getActivityId() !== null) {
                $activityIds[] = $candidate->getActivityId();
            }
        }

        return $activityIds;
    }

    /**
     * @return list<string>
     */
    private function loadExistingFileHashes(User $user): array
    {
        $hashes = [];

        /** @var TrackImportCandidate $candidate */
        foreach ($this->registry->getRepository(TrackImportCandidate::class)->findBy(['user' => $user]) as $candidate) {
            if ($candidate->getSource() === TrackImportCandidate::CANDIDATE_SOURCE_UPLOAD && $candidate->getFileHash() !== null) {
                $hashes[] = $candidate->getFileHash();
            }
        }

        return $hashes;
    }
}
