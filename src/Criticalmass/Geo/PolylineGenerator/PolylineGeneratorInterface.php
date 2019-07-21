<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\PolylineGenerator;

use App\Criticalmass\Geo\PositionList\PositionList;

interface PolylineGeneratorInterface
{
    public function execute(PositionList $positionList): string;
}
