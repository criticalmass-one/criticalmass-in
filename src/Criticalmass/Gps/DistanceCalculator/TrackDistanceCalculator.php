<?php declare(strict_types=1);

namespace App\Criticalmass\Gps\DistanceCalculator;

/**
 * @deprecated
 */
class TrackDistanceCalculator extends AbstractDistanceCalculator
{
    public function calculate(): float
    {
        $startPoint = intval($this->track->getStartPoint());
        $endPoint = intval($this->track->getEndPoint());
        $distance = (float)0.0;

        $index = $startPoint + 1;
        $firstCoord = $this->trackReader->getPoint($startPoint);

        while ($index < $endPoint) {
            $secondCoord = $this->trackReader->getPoint($index);

            $dx = 71.5 * ((float)$firstCoord['lon'] - (float)$secondCoord['lon']);
            $dy = 111.3 * ((float)$firstCoord['lat'] - (float)$secondCoord['lat']);

            $way = (float)sqrt($dx * $dx + $dy * $dy);

            $distance += $way;

            ++$index;

            $firstCoord = $secondCoord;
        }

        return (float)round($distance, 2);
    }
}
