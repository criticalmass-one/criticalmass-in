<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\TimeShifter;

use App\Criticalmass\Geo\Entity\Track;
use App\Criticalmass\Geo\GpxReader\TrackReader;
use App\Criticalmass\Geo\GpxWriter\GpxWriterInterface;

class TrackTimeShifter extends TimeShifter
{
    /** @var TrackReader $trackReader */
    protected $trackReader;

    /** @var Track $track */
    protected $track;

    /** @var GpxWriterInterface $gpxWriter */
    protected $gpxWriter;

    public function __construct(TrackReader $trackReader, GpxWriterInterface $gpxWriter)
    {
        $this->trackReader = $trackReader;
        $this->gpxWriter = $gpxWriter;
    }

    public function loadTrack(Track $track): TrackTimeShifter
    {
        $this->track = $track;

        $this->trackReader->loadTrack($this->track);

        $this->positionList = $this->trackReader->createPositionList();

        return $this;
    }

    public function saveTrack(): TrackTimeShifter
    {
        $this->gpxWriter
            ->setPositionList($this->positionList)
            ->saveGpxContent();

        return $this;
    }
}
