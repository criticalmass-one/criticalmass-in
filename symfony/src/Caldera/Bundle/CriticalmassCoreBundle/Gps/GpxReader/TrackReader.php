<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Gps\GpxReader;

use Caldera\Bundle\CriticalmassCoreBundle\Gps\GpxReader\GpxCoordLoop\GpxCoordLoop;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Track;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class TrackReader extends GpxReader {
    /**
     * @var Track $track
     */
    protected $track;

    public function loadTrack(Track $track)
    {
        $this->track = $track;
        $filename = $this->uploaderHelper->asset($track, 'trackFile');
        
        $this->loadFile($filename);
    }

    public function getStartDateTime()
    {
        $startPoint = intval($this->track->getStartPoint());
        
        return new \DateTime($this->simpleXml->trk->trkseg->trkpt[$startPoint]->time);
    }

    public function getEndDateTime()
    {
        $endPoint = intval($this->track->getEndPoint());
        
        return new \DateTime($this->simpleXml->trk->trkseg->trkpt[$endPoint]->time);
    }

    public function calculateDistance()
    {
        $startPoint = intval($this->track->getStartPoint());
        $endPoint = intval($this->track->getEndPoint());
        $distance = (float) 0.0;

        $index = $startPoint + 1;

        $firstCoord = $this->simpleXml->trk->trkseg->trkpt[$startPoint];

        while ($index < $endPoint) {
            $secondCoord = $this->simpleXml->trk->trkseg->trkpt[$index];

            $dx = 71.5 * ((float) $firstCoord['lon'] - (float) $secondCoord['lon']);
            $dy = 111.3 * ((float) $firstCoord['lat'] - (float) $secondCoord['lat']);

            $way = (float) sqrt($dx * $dx + $dy * $dy);

            $secondTime = new \DateTime($secondCoord->time);
            $firstTime = new \DateTime($firstCoord->time);

            $timeInterval = $secondTime->diff($firstTime);
            $time = $timeInterval->format('%s');
            $time += 0.001;

            $velocity = $way * 1000 / $time;

            if ($velocity > 4.5) {
                $distance += $way;
            }

            $firstCoord = $secondCoord;

            ++$index;
        }

        return (float) round($distance, 2);
    }
}