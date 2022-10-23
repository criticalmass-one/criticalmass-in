<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\DistanceCalculator;

use App\Criticalmass\Geo\Converter\TrackToPositionListConverter;
use App\Criticalmass\Geo\Entity\Track;
use App\Criticalmass\Geo\GpxReader\TrackReader;

class TrackDistanceCalculator extends DistanceCalculator implements TrackDistanceCalculatorInterface
{
    public function __construct(protected TrackReader $trackReader)
    {
    }

    public function setTrack(Track $track): DistanceCalculatorInterface
    {
        $converter = new TrackToPositionListConverter($this->trackReader);

        $this->positionList = $converter->convert($track);

        return $this;
    }
}