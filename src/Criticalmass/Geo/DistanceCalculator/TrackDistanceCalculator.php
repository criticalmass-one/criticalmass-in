<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\DistanceCalculator;

use App\Criticalmass\Geo\Converter\TrackToPositionListConverter;
use App\Criticalmass\Geo\Entity\Track;
use App\Criticalmass\Geo\GpxReader\TrackReader;

class TrackDistanceCalculator extends DistanceCalculator implements TrackDistanceCalculatorInterface
{
    /** @var TrackReader $trackReader */
    protected $trackReader;

    public function __construct(TrackReader $trackReader)
    {
        $this->trackReader = $trackReader;
    }

    public function setTrack(Track $track): DistanceCalculatorInterface
    {
        $converter = new TrackToPositionListConverter($this->trackReader);

        $this->positionList = $converter->convert($track);

        return $this;
    }
}