<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Gps\TrackTimeshift;

use AppBundle\Entity\Track;

/**
 * @deprecated
 */
interface TrackTimeshiftInterface
{
    public function loadTrack(Track $track): TrackTimeshiftInterface;

    public function shift(\DateInterval $interval): TrackTimeshiftInterface;

    public function saveTrack(): TrackTimeshiftInterface;
}
