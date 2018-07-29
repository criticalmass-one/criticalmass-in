<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\LatLngListGenerator;

class RangeLatLngListGenerator extends AbstractLatLngListGenerator
{
    public function execute()
    {
        $start = $this->track->getStartPoint();
        $end = $this->track->getEndPoint();

        $result = array();

        $counter = 0;

        foreach ($this->xmlRootNode->trk->trkseg->trkpt as $point) {
            if ($counter >= $start && $counter < $end && $counter % $this->gapWidth == 0) {
                $result[] = '[' . $point['lat'] . ',' . $point['lon'] . ']';
            }

            ++$counter;
        }

        $this->list = '[' . implode(',', $result) . ']';

        return $this;
    }
} 
