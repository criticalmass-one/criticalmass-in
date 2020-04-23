<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\Loop;

use App\Criticalmass\Geo\Converter\TrackToPositionListConverter;
use App\Criticalmass\Geo\Entity\Track;
use App\Criticalmass\Geo\GpxReader\TrackReader;

class TrackLoop extends Loop
{
    /** @var TrackReader $trackReader */
    protected $trackReader;

    public function __construct(TrackReader $trackReader)
    {
        $this->trackReader = $trackReader;

        parent::__construct();
    }

    public function loadTrack(Track $track): TrackLoop
    {
        $converter = new TrackToPositionListConverter($this->trackReader);
        $this->positionList = $converter->convert($track);

        return $this;
    }
}
