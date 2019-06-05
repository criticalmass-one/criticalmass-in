<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\GpxReader;

use App\Criticalmass\Geo\Entity\Track;

class TrackReader extends GpxReader
{
    /** @var Track $track */
    protected $track;

    public function loadTrack(Track $track): TrackReader
    {
        $this->track = $track;

        $this->loadFromFile($track->getTrackFilename());

        return $this;
    }

    public function getStartDateTime(): \DateTime
    {
        $startPoint = $this->track->getStartPoint();

        return new \DateTime((string) $this->trackPointList[$startPoint]->time);
    }

    public function getEndDateTime(): \DateTime
    {
        $endPoint = $this->track->getEndPoint() - 1;

        return new \DateTime((string) $this->trackPointList[$endPoint]->time);
    }
}
