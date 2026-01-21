<?php declare(strict_types=1);

namespace App\MessageHandler;

use App\Criticalmass\MassTrackImport\ProposalPersister\ProposalPersisterInterface;
use App\Criticalmass\MassTrackImport\TrackDecider\TrackDeciderInterface;
use App\Entity\TrackImportCandidate;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class TrackImportCandidateMessageHandler
{
    public function __construct(
        private readonly ProposalPersisterInterface $proposalPersister,
        private readonly TrackDeciderInterface $trackDecider,
    ) {
    }

    public function __invoke(TrackImportCandidate $candidate): void
    {
        $rideResult = $this->trackDecider->decide($candidate);

        if ($rideResult) {
            $this->proposalPersister->persist($rideResult);
        }
    }
}
