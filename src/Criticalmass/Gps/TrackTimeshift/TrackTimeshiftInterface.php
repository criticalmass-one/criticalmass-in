<?php declare(strict_types=1);

namespace App\Criticalmass\Gps\TrackTimeshift;

use App\Entity\Track;

/**
 * @deprecated
 */
interface TrackTimeshiftInterface
{
    public function loadTrack(Track $track): TrackTimeshiftInterface;

    public function shift(\DateInterval $interval): TrackTimeshiftInterface;

    public function saveTrack(): TrackTimeshiftInterface;
}
