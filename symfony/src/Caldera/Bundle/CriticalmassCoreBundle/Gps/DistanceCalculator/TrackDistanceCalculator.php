<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Gps\DistanceCalculator;


use Caldera\Bundle\CriticalmassCoreBundle\Gps\GpxReader\TrackReader;
use Caldera\Bundle\CalderaBundle\Entity\Track;

class TrackDistanceCalculator extends BaseDistanceCalculator
{
    protected $doctrine;

    /**
     * @var TrackReader $trackReader
     */
    protected $trackReader;

    /**
     * @var Track $track
     */
    protected $track;

    public function __construct($doctrine, TrackReader $trackReader)
    {
        $this->doctrine = $doctrine;
        $this->trackReader = $trackReader;
    }

    public function loadTrack(Track $track)
    {
        $this->track = $track;
        $this->trackReader->loadTrack($track);

        return $this;
    }

    public function calculate()
    {
        $startPoint = intval($this->track->getStartPoint());
        $endPoint = intval($this->track->getEndPoint());
        $distance = (float) 0.0;

        $index = $startPoint + 1;
        $firstCoord = $this->trackReader->getPoint($startPoint);

        while ($index < $endPoint) {
            $secondCoord = $this->trackReader->getPoint($index);

            $dx = 71.5 * ((float) $firstCoord['lon'] - (float) $secondCoord['lon']);
            $dy = 111.3 * ((float) $firstCoord['lat'] - (float) $secondCoord['lat']);

            $way = (float) sqrt($dx * $dx + $dy * $dy);

            $distance += $way;

            ++$index;

            $firstCoord = $secondCoord;
        }

        return (float) round($distance, 2);
    }
}