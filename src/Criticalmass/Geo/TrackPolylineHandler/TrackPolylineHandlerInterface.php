<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\TrackPolylineHandler;

use App\Entity\Track;

interface TrackPolylineHandlerInterface
{
    public function handleTrack(Track $track): Track;
}
