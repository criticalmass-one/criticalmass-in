<?php

namespace App\Criticalmass\Gps\TrackChecker;

use App\Entity\Track;

/**
 * @deprecated
 */
interface TrackCheckerInterface
{
    public function loadTrack(Track $track);

    public function isValid();
}
