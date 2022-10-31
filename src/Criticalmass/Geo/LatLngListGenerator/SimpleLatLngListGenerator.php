<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\LatLngListGenerator;

class SimpleLatLngListGenerator extends AbstractLatLngListGenerator
{
    public function execute()
    {
        $result = [];

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
