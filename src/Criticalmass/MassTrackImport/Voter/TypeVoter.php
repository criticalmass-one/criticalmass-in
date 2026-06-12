<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\Voter;

use App\Entity\Ride;
use App\Entity\TrackImportCandidate;

class TypeVoter implements VoterInterface
{
    public function vote(Ride $ride, TrackImportCandidate $model): float
    {
        // Uploaded files carry no Strava activity "type" — never disqualify them here.
        if ($model->getSource() === TrackImportCandidate::CANDIDATE_SOURCE_UPLOAD) {
            return 1;
        }

        if ($model->getType() !== 'Ride') {
            return -1;
        }

        return 1;
    }
}
