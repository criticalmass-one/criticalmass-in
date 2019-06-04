<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\DistanceCalculator;

use App\Criticalmass\Geo\Entity\Track;

interface TrackDistanceCalculatorInterface extends DistanceCalculatorInterface
{
    public function setTrack(Track $track): DistanceCalculatorInterface;
}