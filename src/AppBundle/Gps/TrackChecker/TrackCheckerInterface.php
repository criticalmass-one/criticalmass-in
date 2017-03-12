<?php

namespace AppBundle\Gps\TrackChecker;

use AppBundle\Entity\Track;

interface TrackCheckerInterface
{
    public function loadTrack(Track $track);

    public function isValid();
}