<?php

namespace AppBundle\Criticalmass\Gps\TrackChecker;

use AppBundle\Entity\Track;

/**
 * @deprecated
 */
interface TrackCheckerInterface
{
    public function loadTrack(Track $track);

    public function isValid();
}
