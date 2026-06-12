<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\ProposalPersister;

use App\Criticalmass\MassTrackImport\TrackDecider\RideResult;
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
        $fileHash = $candidate->getFileHash();

        return $fileHash !== null && in_array($fileHash, $this->loadExistingFileHashes($candidate->getUser()), true);
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
