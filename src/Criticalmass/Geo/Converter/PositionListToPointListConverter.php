<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\Converter;

use App\Criticalmass\Geo\EntityInterface\PositionInterface;
use App\Criticalmass\Geo\PositionList\PositionListInterface;

class PositionListToPointListConverter
{
    private function __construct()
    {

    }

    public static function convert(PositionListInterface $positionList): array
    {
        $pointList = [];

        /** @var PositionInterface $position */
        for ($n = 0, $nMax = count($positionList); $n < $nMax; ++$n) {
            $pointList[$n] = $positionList->get($n)->toArray();
        }

        return $pointList;
    }
}