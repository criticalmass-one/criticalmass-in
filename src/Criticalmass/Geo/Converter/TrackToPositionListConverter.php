<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\Converter;

use App\Criticalmass\Geo\GpxReader\TrackReader;
use App\Criticalmass\Geo\PositionList\PositionList;
use App\Criticalmass\Geo\PositionList\PositionListInterface;
use App\Entity\Track;

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

        for ($n = 0; $n < $this->trackReader->countPoints(); ++$n) {
            $gpxPoint = $this->trackReader->getPoint($n);

            $position = GpxPointToPositionConverter::convert($gpxPoint);

            $positionList->add($position);
        }

        return $positionList;
    }
}