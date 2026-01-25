<?php declare(strict_types=1);

namespace App\MessageHandler;

use App\Criticalmass\MassTrackImport\ProposalPersister\ProposalPersisterInterface;
use App\Criticalmass\MassTrackImport\TrackDecider\TrackDeciderInterface;
use App\Entity\TrackImportCandidate;
use App\Entity\User;
use App\Message\ImportTrackCandidateMessage;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ImportTrackCandidateMessageHandler
{
    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly ProposalPersisterInterface $proposalPersister,
        private readonly TrackDeciderInterface $trackDecider
    ) {
    }

    public function __invoke(ImportTrackCandidateMessage $message): void
    {
        $user = $this->registry->getRepository(User::class)->find($message->getUserId());

        if (!$user) {
            return;
        }

        $candidate = new TrackImportCandidate();
        $candidate
            ->setUser($user)
            ->setActivityId($message->getActivityId())
            ->setName($message->getName())
            ->setDistance($message->getDistance())
            ->setElapsedTime($message->getElapsedTime())
            ->setType($message->getType())
            ->setStartDateTime(\DateTime::createFromInterface($message->getStartDateTime()))
            ->setStartLatitude($message->getStartLatitude())
            ->setStartLongitude($message->getStartLongitude())
            ->setEndLatitude($message->getEndLatitude())
            ->setEndLongitude($message->getEndLongitude())
            ->setPolyline($message->getPolyline());

        $rideResult = $this->trackDecider->decide($candidate);

        if ($rideResult) {
            $this->proposalPersister->persist($rideResult);
        }
    }
}
