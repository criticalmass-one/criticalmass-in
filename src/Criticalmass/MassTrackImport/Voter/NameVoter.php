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

        if (str_contains($modelName, 'Critical Mass')) {
            return 0.95;
        }

        if (str_contains($modelName, 'Critical') && str_contains($modelName, 'Mass')) {
            return 0.95;
        }

        if (str_contains($modelName, 'Critical') || str_contains($modelName, 'Mass')) {
            return 0.8;
        }

        if (str_contains($modelName, $ride->getCity()->getCity())) {
            return 0.5;
        }

        return 0.0;
    }
}
