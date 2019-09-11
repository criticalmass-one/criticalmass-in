<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\TrackDecider;

use App\Criticalmass\MassTrackImport\Voter\VoterInterface;
use App\Entity\TrackImportCandidate;

interface TrackDeciderInterface
{
    public function decide(TrackImportCandidate $model): ?RideResult;

    public function addVoter(VoterInterface $voter): TrackDeciderInterface;
}
