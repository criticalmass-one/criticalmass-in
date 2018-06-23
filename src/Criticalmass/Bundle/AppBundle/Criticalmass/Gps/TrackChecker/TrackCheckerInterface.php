<?php

namespace Criticalmass\Bundle\AppBundle\Criticalmass\Gps\TrackChecker;

use Criticalmass\Bundle\AppBundle\Entity\Track;

/**
 * @deprecated
 */
interface TrackCheckerInterface
{
    public function loadTrack(Track $track);

    public function isValid();
}
