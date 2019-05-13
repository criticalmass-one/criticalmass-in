<?php declare(strict_types=1);

namespace App\Criticalmass\Strava\TrackImporter;

use App\Entity\Track;

interface TrackImporterInterface
{
    public function doMagic(int $activityId): Track;
}
