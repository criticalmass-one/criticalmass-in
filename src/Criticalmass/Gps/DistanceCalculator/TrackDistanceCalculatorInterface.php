<?php declare(strict_types=1);

namespace App\Criticalmass\Gps\DistanceCalculator;

use App\Entity\Track;

/**
 * @deprecated
 */
interface TrackDistanceCalculatorInterface
{
    public function loadTrack(Track $track): TrackDistanceCalculatorInterface;
    public function calculate(): float;
}
