<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\Voter;

use App\Criticalmass\MassTrackImport\Model\StravaActivityModel;
use App\Entity\Ride;
use Carbon\Carbon;

class DateTimeVoter implements VoterInterface
{
    public function vote(Ride $ride, StravaActivityModel $model): float
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

        if (abs($diff) < 15) {
            return 1.0;
        }

        if (-30 <= $diff) {
            return 0.9;
        }

        if (30 >= $diff) {
            return 1.0;
        }

        if (-45 <= $diff) {
            return 0.8;
        }

        if (45 >= $diff) {
            return 0.9;
        }

        if (-90 <= $diff || 90 >= $diff) {
            return 0.6;
        }

        if (-180 <= $diff || 180 >= $diff) {
            return 0.5;
        }

        if (-240 <= $diff || 240 >= $diff) {
            return 0.3;
        }

        if ($rideDateTime->isSameDay($modelDateTime)) {
            return 0.25;
        }

        return 0;
    }
}
