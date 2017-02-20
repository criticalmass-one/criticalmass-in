<?php

namespace Caldera\Bundle\CalderaBundle\Gps\TrackChecker;

use Caldera\Bundle\CalderaBundle\Entity\Track;

interface TrackCheckerInterface
{
    public function loadTrack(Track $track);

    public function isValid();
}