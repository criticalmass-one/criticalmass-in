<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\Voter;

use App\Criticalmass\MassTrackImport\Model\StravaActivityModel;
use App\Entity\Ride;

interface VoterInterface
{
    public function vote(Ride $ride, StravaActivityModel $model): float;
}