<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\Voter;

use App\Criticalmass\MassTrackImport\Model\StravaActivityModel;
use App\Entity\Ride;

class DurationVoter implements VoterInterface
{
    public function vote(Ride $ride, StravaActivityModel $model): float
    {
        $duration = $model->getElapsedTime();

        if ($duration > 45 * 60 && $duration < 3 * 60 * 60) {
            return 0.75;
        }

        if ($duration > 15 * 60 && $duration < 6 * 60 * 60) {
            return 0.5;
        }

        return 0;
    }
}
