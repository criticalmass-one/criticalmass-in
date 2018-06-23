<?php declare(strict_types=1);

namespace Criticalmass\Component\Gps\TrackTimeshift;

use Criticalmass\Bundle\AppBundle\Entity\Track;

/**
 * @deprecated
 */
interface TrackTimeshiftInterface
{
    public function loadTrack(Track $track): TrackTimeshiftInterface;

    public function shift(\DateInterval $interval): TrackTimeshiftInterface;

    public function saveTrack(): TrackTimeshiftInterface;
}
