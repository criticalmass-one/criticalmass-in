<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\TimeShifter;

use Caldera\GeoBundle\Entity\Track;
use Caldera\GeoBundle\GpxReader\TrackReader;

class TrackTimeShifter extends TimeShifter
{
    /** @var TrackReader $trackReader */
    protected $trackReader;

    /** @var Track $track */
    protected $track;

    public function __construct(TrackReader $trackReader)
    {
        $this->trackReader = $trackReader;
    }

    public function loadTrack(Track $track): TrackTimeShifter
    {
        $this->track = $track;

        $this->trackReader->loadTrack($this->track);

        $this->positionList = $this->trackReader->createPositionList();

        return $this;
    }
}
