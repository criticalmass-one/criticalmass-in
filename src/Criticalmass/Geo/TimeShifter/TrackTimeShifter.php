<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\TimeShifter;

use App\Criticalmass\Geo\Converter\TrackToPositionListConverter;
use App\Criticalmass\Geo\Entity\Track;
use App\Criticalmass\Geo\GpxWriter\GpxWriterInterface;

class TrackTimeShifter extends TimeShifter implements TrackTimeShifterInterface
{
    /** @var TrackToPositionListConverter $trackToPositionListConverter */
    protected $trackToPositionListConverter;

    /** @var Track $track */
    protected $track;

    /** @var GpxWriterInterface $gpxWriter */
    protected $gpxWriter;

    public function __construct(TrackToPositionListConverter $trackToPositionListConverter, GpxWriterInterface $gpxWriter)
    {
        $this->trackToPositionListConverter = $trackToPositionListConverter;
        $this->gpxWriter = $gpxWriter;
    }

    public function loadTrack(Track $track): TrackTimeShifterInterface
    {
        $this->track = $track;

        $this->positionList = $this->trackToPositionListConverter->convert($track);

        return $this;
    }

    public function saveTrack(): TrackTimeShifterInterface
    {
        $this->gpxWriter
            ->setPositionList($this->positionList)
            ->generateGpxContent()
            ->saveGpxContent($this->track->getTrackFilename());

        return $this;
    }
}
