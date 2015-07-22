<?php

namespace Caldera\CriticalmassCoreBundle\Utility\LatLngArrayGenerator;

use Caldera\CriticalmassCoreBundle\Entity\Track;
use Caldera\CriticalmassCoreBundle\Utility\GpxReader\GpxReader;

class SimpleLatLngArrayGenerator extends AbstractLatLngArrayGenerator
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