<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\Voter;

use App\Entity\Ride;
use App\Entity\TrackImportCandidate;
use Carbon\Carbon;

class DateTimeVoter implements VoterInterface
{
    public function vote(Ride $ride, TrackImportCandidate $model): float
    {
        $rideDateTime = Carbon::instance($ride->getDateTime());
        $modelDateTime = Carbon::instance($model->getStartDateTime());

        if (!$rideDateTime->isSameDay($modelDateTime)) {
            return -1;
        }

        if ($rideDateTime->equalTo($modelDateTime)) {
            return 1;
        }

        $diff = $rideDateTime->diffInMinutes($modelDateTime, false);

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

        if ($rideDateTime->isSameDay($modelDateTime)) {
            return 0.25;
        }

        return 0;
    }
}
