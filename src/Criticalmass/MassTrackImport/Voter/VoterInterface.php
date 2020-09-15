<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\Voter;

use App\Entity\Ride;
use App\Entity\TrackImportCandidate;

interface VoterInterface
{
    public function vote(Ride $ride, TrackImportCandidate $model): float;
}