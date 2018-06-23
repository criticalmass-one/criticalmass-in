<?php declare(strict_types=1);

namespace Criticalmass\Component\Gps\DistanceCalculator;

use Criticalmass\Bundle\AppBundle\Entity\Track;

/**
 * @deprecated
 */
interface TrackDistanceCalculatorInterface
{
    public function loadTrack(Track $track): TrackDistanceCalculatorInterface;
    public function calculate(): float;
}
