<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\Converter;

use App\Criticalmass\Geo\Entity\Track;
use App\Criticalmass\Geo\GpxReader\TrackReader;
use App\Criticalmass\Geo\PositionList\PositionList;
use App\Criticalmass\Geo\PositionList\PositionListInterface;

class TrackToPositionListConverter
{
    /** @var TrackReader $trackReader */
    protected $trackReader;

    public function __construct(TrackReader $trackReader)
    {
        $this->trackReader = $trackReader;
    }

    public function convert(Track $track): PositionListInterface
    {
        $this->trackReader->loadTrack($track);

        $positionList = new PositionList();

        for ($n = $track->getStartPoint(); $n <= $track->getEndPoint(); ++$n) {
            $gpxPoint = $this->trackReader->getPoint($n);

            $position = GpxPointToPositionConverter::convert($gpxPoint);

            $positionList->add($position);
        }

        return $positionList;
    }
}
