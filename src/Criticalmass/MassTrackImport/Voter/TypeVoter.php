<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\Voter;

use App\Entity\Ride;
use App\Entity\TrackImportProposal;

class TypeVoter implements VoterInterface
{
    public function vote(Ride $ride, TrackImportProposal $model): float
    {
        if ($model->getType() !== 'Ride') {
            return -1;
        }

        return 1;
    }
}
