<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\Voter;

use App\Criticalmass\MassTrackImport\Model\StravaActivityModel;
use App\Entity\Ride;

class NameVoter implements VoterInterface
{
    public function vote(Ride $ride, StravaActivityModel $model): float
    {
        if ($ride->getTitle() === $model->getName()) {
            return 1.0;
        }

        if (strpos('Critical', $model->getName()) !== false && strpos('Mass', $model->getName()) !== false) {
            return 0.9;
        }

        if (strpos('Critical', $model->getName()) !== false || strpos('Mass', $model->getName()) !== false) {
            return 0.8;
        }

        return 0.0;
    }
}
