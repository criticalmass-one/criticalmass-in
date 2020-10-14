<?php declare(strict_types=1);

namespace App\Criticalmass\Strava\Importer;

use App\Entity\Ride;
use App\Entity\Track;
use App\Entity\User;

interface TrackImporterInterface
{
    public function setUser(User $user): TrackImporterInterface;
    public function setRide(Ride $ride): TrackImporterInterface;
    public function setStravaActivityId(int $activityId): TrackImporterInterface;
    public function importTrack(): Track;
}
