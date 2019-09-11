<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\Voter;

use App\Entity\Ride;
use App\Entity\TrackImportCandidate;

class NameVoter implements VoterInterface
{
    public function vote(Ride $ride, TrackImportCandidate $model): float
    {
        $rideTitle = $ride->getTitle();
        $modelName = $model->getName();

        if ($rideTitle === $modelName) {
            return 1.0;
        }

        if (strpos($modelName, 'Critical Mass') !== false) {
            return 0.95;
        }

        if (strpos($modelName, 'Critical') !== false && strpos($modelName, 'Mass') !== false) {
            return 0.95;
        }

        if (strpos($modelName, 'Critical') !== false || strpos($modelName, 'Mass') !== false) {
            return 0.8;
        }

        if (strpos($modelName, $ride->getCity()->getCity()) !== false) {
            return 0.5;
        }

        return 0.0;
    }
}
