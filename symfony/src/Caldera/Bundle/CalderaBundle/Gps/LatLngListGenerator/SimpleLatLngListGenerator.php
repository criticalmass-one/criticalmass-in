<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Gps\LatLngListGenerator;

class SimpleLatLngListGenerator extends AbstractLatLngListGenerator
{
    public function execute()
    {
        $result = array();

        $counter = 0;

        foreach ($this->xmlRootNode->trk->trkseg->trkpt as $point) {
            if ($counter % $this->gapWidth == 0) {
                $result[] = '[' . $point['lat'] . ',' . $point['lon'] . ']';
            }

            ++$counter;
        }

        $this->list = '[' . implode(',', $result) . ']';

        return $this;
    }
} 