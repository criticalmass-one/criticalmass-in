<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\TimeShifter;

use App\Criticalmass\Geo\Entity\Track;

interface TrackTimeShifterInterface extends TimeShifterInterface
{
    public function loadTrack(Track $track): TrackTimeShifterInterface;
    public function saveTrack(): TrackTimeShifterInterface;
}