<?php

namespace Criticalmass\Bundle\AppBundle\Gps\TrackChecker;

use Criticalmass\Bundle\AppBundle\Entity\Track;

/**
 * @deprecated
 */
interface TrackCheckerInterface
{
    public function loadTrack(Track $track);

    public function isValid();
}
