<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\DistanceCalculator;

class DistanceCalculator extends AbstractDistanceCalculator
{
    public function calculate(): float
    {
        $distance = 0.0;

        if (1 >= count($this->positionList)) {
            return 0.0;
        }

        $position1 = $this->positionList->get(0);

        for ($i = 1; $i < count($this->positionList); ++$i) {
            $position2 = $this->positionList->get($i);

            $dx = 71.5 * ($position1->getLongitude() - $position2->getLongitude());
            $dy = 111.3 * ($position1->getLatitude() - $position2->getLatitude());

            $distance += sqrt($dx * $dx + $dy * $dy);

            $position1 = $position2;
        }

        return $distance;
    }
}
