<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\PolylineGenerator;

use App\Criticalmass\Geo\Converter\PositionListToPointListConverter;
use App\Criticalmass\Geo\PositionList\PositionList;

class PolylineGenerator extends AbstractPolylineGenerator
{
    public function execute(PositionList $positionList): string
    {
        $pointList = PositionListToPointListConverter::convert($positionList);

        return $this->polylineStrategy->generate($pointList);
    }
} 
