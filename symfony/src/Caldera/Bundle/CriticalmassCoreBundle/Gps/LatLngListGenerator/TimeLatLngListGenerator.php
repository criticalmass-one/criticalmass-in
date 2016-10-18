<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Gps\LatLngListGenerator;

class TimeLatLngListGenerator extends AbstractLatLngListGenerator
{
    public function execute()
    {
        $start = $this->track->getStartPoint();
        $end = $this->track->getEndPoint();

        $result = array();

        $counter = 0;

        foreach ($this->xmlRootNode->trk->trkseg->trkpt as $point) {
            if ($counter >= $start && $counter < $end && $counter % $this->gapWidth == 0) {
                $result[] = '["' . $point->time . '",' . $point['lat'] . ',' . $point['lon'] . ']';
            }

            ++$counter;
        }

        $this->list = '[' . implode(',', $result) . ']';

        return $this;
    }
} 