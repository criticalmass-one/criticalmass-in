<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\ProposalPersister;

use App\Criticalmass\MassTrackImport\TrackDecider\RideResult;

interface ProposalPersisterInterface
{
    public function persist(RideResult $rideResult): RideResult;
}