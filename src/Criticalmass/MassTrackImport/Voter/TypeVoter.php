<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\Voter;

use App\Criticalmass\MassTrackImport\Model\StravaActivityModel;
use App\Entity\Ride;

class TypeVoter implements VoterInterface
{
    public function vote(Ride $ride, StravaActivityModel $model): float
    {
        if ($model->getType() !== 'Ride') {
            return -1;
        }

        return 1;
    }
}
