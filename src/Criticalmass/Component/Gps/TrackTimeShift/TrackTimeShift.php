<?php

namespace Criticalmass\Component\Gps\TrackTimeShift;

use Criticalmass\Bundle\AppBundle\Entity\Position;
use Criticalmass\Bundle\AppBundle\Entity\Track;
use Criticalmass\Component\Gps\GpxExporter\GpxExporter;
use Criticalmass\Component\Gps\GpxReader\TrackReader;

/**
 * @deprecated
 */
class TrackTimeShift
{
    protected $doctrine;
    protected $trackReader;
    protected $gpxExporter;

    /**
     * @var Track $track
     */
    protected $track;

    protected $positionArray;

    public function __construct($doctrine, TrackReader $trackReader, GpxExporter $gpxExporter)
    {
        $this->doctrine = $doctrine;
        $this->trackReader = $trackReader;
        $this->gpxExporter = $gpxExporter;
    }

    public function loadTrack(Track $track)
    {
        $this->track = $track;

        $this->trackReader->loadTrack($this->track);

        $this->positionArray = $this->trackReader->getAsPositionArray();

        return $this;
    }

    public function shift(\DateInterval $interval)
    {
        /**
         * @var Position $position
         */
        foreach ($this->positionArray as $position) {
            $dateTime = new \DateTime();
            $dateTime->setTimestamp($position->getTimestamp());
            $dateTime->sub($interval);
            $position->setTimestamp($dateTime->getTimestamp());
        }

        return $this;
    }

    public function saveTrack()
    {
        $this->gpxExporter->setPositionArray($this->positionArray);

        $this->gpxExporter->execute();

        $gpxContent = $this->gpxExporter->getGpxContent();

        $filename = $this->track->getTrackFilename();

        $fp = fopen('../web/tracks/' . $filename, 'w');
        fwrite($fp, $gpxContent);
        fclose($fp);
    }
}
