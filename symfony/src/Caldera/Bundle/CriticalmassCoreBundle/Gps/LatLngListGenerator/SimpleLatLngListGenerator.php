<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Gps\LatLngArrayGenerator;

class SimpleLatLngListGenerator extends AbstractLatLngListGenerator
{
    public function execute()
    {
        $result = array();

        $counter = 0;

        foreach ($this->xmlRootNode->trk->trkseg->trkpt as $point)
        {
            if ($counter % 5 == 0)
            {
                $result[] = '['.$point['lat'].','.$point['lon'].']';
            }

            ++$counter;
        }

        $this->json = '['.implode(',', $result).']';
    }
} 