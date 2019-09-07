<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\Voter;

use App\Entity\Ride;
use App\Entity\TrackImportProposal;

interface VoterInterface
{
    public function vote(Ride $ride, TrackImportProposal $model): float;
}