<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\TrackDecider;

use App\Criticalmass\MassTrackImport\Model\StravaActivityModel;
use App\Criticalmass\MassTrackImport\Voter\VoterInterface;

interface TrackDeciderInterface
{
    public function decide(StravaActivityModel $model): ?RideResult;

    public function addVoter(VoterInterface $voter): TrackDeciderInterface;
}
