<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\TrackDecider;

use App\Criticalmass\MassTrackImport\Voter\VoterInterface;
use App\Entity\TrackImportProposal;

interface TrackDeciderInterface
{
    public function decide(TrackImportProposal $model): ?RideResult;

    public function addVoter(VoterInterface $voter): TrackDeciderInterface;
}
