<?php declare(strict_types=1);

namespace Criticalmass\Component\Gps\TrackTimeShift;

use Criticalmass\Bundle\AppBundle\Entity\Track;

/**
 * @deprecated
 */
interface TrackTimeShiftInterface
{
    public function loadTrack(Track $track): TrackTimeShiftInterface;

    public function shift(\DateInterval $interval): TrackTimeShiftInterface;

    public function saveTrack(): TrackTimeShiftInterface;
}
