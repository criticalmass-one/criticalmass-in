<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\Converter;

use App\Criticalmass\Geo\Entity\Track;
use App\Criticalmass\Geo\GpxReader\TrackReader;
use App\Criticalmass\Geo\PositionList\PositionList;
use App\Criticalmass\Geo\PositionList\PositionListInterface;

class TrackToPositionListConverter
{
    public function __construct(protected TrackReader $trackReader)
    {
    }

    public function convert(Track $track): PositionListInterface
    {
        $this->trackReader->loadTrack($track);

        $positionList = new PositionList();

        for ($n = $track->getStartPoint(); $n < $track->getEndPoint(); ++$n) {
            $gpxPoint = $this->trackReader->getPoint($n);

            $position = GpxPointToPositionConverter::convert($gpxPoint);

            $positionList->add($position);
        }

        return $positionList;
    }
}
