<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\Voter;

use App\Entity\Ride;
use App\Entity\TrackImportCandidate;

class DateTimeVoter implements VoterInterface
{
    public function vote(Ride $ride, TrackImportCandidate $model): float
    {
        $rideDateTime = $ride->getDateTime();
        $modelDateTime = $model->getStartDateTime();

        if ($rideDateTime->format('Y-m-d') !== $modelDateTime->format('Y-m-d')) {
            return -1;
        }

        if ($rideDateTime->getTimestamp() === $modelDateTime->getTimestamp()) {
            return 1;
        }

        $diff = ($modelDateTime->getTimestamp() - $rideDateTime->getTimestamp()) / 60;

        if (abs($diff) <= 15) {
            return 1.0;
        }

        if (abs($diff) <= 30) {
            return 0.9;
        }

        if (abs($diff) <= 45) {
            return 0.8;
        }

        if (abs($diff) <= 90) {
            return 0.7;
        }

        if (abs($diff) <= 180) {
            return 0.5;
        }

        if (abs($diff) <= 240) {
            return 0.3;
        }

        return 0.25;
    }
}
