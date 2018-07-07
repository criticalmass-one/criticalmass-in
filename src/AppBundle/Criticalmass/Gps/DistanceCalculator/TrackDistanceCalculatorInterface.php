<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Gps\DistanceCalculator;

use AppBundle\Entity\Track;

/**
 * @deprecated
 */
interface TrackDistanceCalculatorInterface
{
    public function loadTrack(Track $track): TrackDistanceCalculatorInterface;
    public function calculate(): float;
}
