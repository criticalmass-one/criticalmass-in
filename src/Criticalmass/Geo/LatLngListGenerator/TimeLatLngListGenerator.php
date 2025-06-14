<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\LatLngListGenerator;

class TimeLatLngListGenerator extends AbstractLatLngListGenerator
{
    public function execute(): self
    {
        $start = $this->track->getStartPoint();
        $end = $this->track->getEndPoint();

        $result = [];
        $counter = 0;

        foreach ($this->xmlRootNode->trk->trkseg->trkpt as $point) {
            if ($counter >= $start && $counter < $end && $counter % self::WIDTH === 0) {
                $result[] = '["' . $point->time . '",' . $point['lat'] . ',' . $point['lon'] . ']';
            }

            ++$counter;
        }

        $this->list = '[' . implode(',', $result) . ']';

        return $this;
    }
} 
