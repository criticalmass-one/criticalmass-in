<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\GpxReader;

use App\Entity\Track;

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
        $startPoint = intval($this->track->getStartPoint());

        return new \DateTime($this->rootNode->trk->trkseg->trkpt[$startPoint]->time);
    }

    public function getEndDateTime(): \DateTime
    {
        $endPoint = intval($this->track->getEndPoint()) - 1;

        return new \DateTime($this->rootNode->trk->trkseg->trkpt[$endPoint]->time);
    }

    public function slicePublicCoords(): array
    {
        // array_slice will not work on xml tree, so we do this manually

        $startPoint = intval($this->track->getStartPoint());
        $endPoint = intval($this->track->getEndPoint());

        $coordArray = [];

        for ($index = $startPoint; $index < $endPoint; ++$index) {
            $coordArray[$index] = [
                $this->getLatitudeOfPoint($index),
                $this->getLongitudeOfPoint($index)
            ];
        }

        return $coordArray;
    }
}
