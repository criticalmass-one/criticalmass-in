<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Path;

use App\Criticalmass\Geo\Entity\Position;
use App\Criticalmass\Geo\PositionList\PositionListInterface;
use Caldera\GeoBasic\Coord\Coord;

class PositionListToPathListConverter
{
    private function __construct()
    {

    }

    public static function convert(PositionListInterface $positionList): PathList
    {
        $pathList = new PathList();

        $counter = count($positionList);

        if (0 === $counter) {
            return $pathList;
        }

        for ($i = 1; $i < $counter; ++$i) {
            $firstPosition = $positionList->get($i - 1);
            $secondPosition = $positionList->get($i);

            $path = new Path(self::convertPositionToCoord($firstPosition), self::convertPositionToCoord($secondPosition));

            $pathList->add($path);
        }

        return $pathList;
    }

    public static function convertPositionToCoord(Position $position): Coord
    {
        return new Coord($position->getLatitude(), $position->getLongitude());
    }
}
