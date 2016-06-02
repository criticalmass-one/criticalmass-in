<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Gps\TrackChecker;

use Caldera\Bundle\CalderaBundle\Entity\Track;

interface TrackCheckerInterface {
    public function loadTrack(Track $track);
    public function isValid();
}