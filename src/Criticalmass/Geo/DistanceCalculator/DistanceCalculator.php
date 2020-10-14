<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\DistanceCalculator;

use Caldera\GeoBasic\Coord\CoordInterface;

class DistanceCalculator extends AbstractDistanceCalculator
{
    public function calculate(): float
    {
        $distance = 0.0;

        if (1 >= count($this->positionList)) {
            return $distance;
        }

        $position1 = $this->positionList->get(0);

        for ($i = 1, $iMax = count($this->positionList); $i < $iMax; ++$i) {
            $position2 = $this->positionList->get($i);

            $distance += self::calculateDistance($position1, $position2);
            
            $position1 = $position2;
        }

        return $distance;
    }

    public static function calculateDistance(CoordInterface $coordA, CoordInterface $coordB): float
    {
        $dx = 71.5 * ($coordA->getLongitude() - $coordB->getLongitude());
        $dy = 111.3 * ($coordA->getLatitude() - $coordB->getLatitude());

        return sqrt($dx * $dx + $dy * $dy);
    }
}
