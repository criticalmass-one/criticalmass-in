<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\TilePrinter;

use App\Criticalmass\Heatmap\Brush\Brush;
use App\Criticalmass\Heatmap\CoordCalculator\CoordCalculator;
use App\Criticalmass\Heatmap\Pipette\Pipette;
use App\Criticalmass\Heatmap\Tile\Tile;
use Caldera\GeoBasic\Coord\CoordInterface;
use Imagine\Image\Palette\RGB as RGBPalette;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;

class TilePrinter
{
    public static function printTile(Tile $tile, CoordInterface $coord): Tile
    {
        $tileTopLatitude = CoordCalculator::yTileToLatitude($tile->getTileY(), $tile->getZoomLevel());
        $tileLeftLongitude = CoordCalculator::xTileToLongitude($tile->getTileX(), $tile->getZoomLevel());
        $tileBottomLatitude = CoordCalculator::yTileToLatitude($tile->getTileY() + 1, $tile->getZoomLevel());
        $tileRightLongitude = CoordCalculator::xTileToLongitude($tile->getTileX() + 1, $tile->getZoomLevel());

        $y = Tile::SIZE * ($tileTopLatitude - $coord->getLatitude()) / ($tileTopLatitude - $tileBottomLatitude);
        $x = Tile::SIZE * ($coord->getLongitude() - $tileLeftLongitude) / ($tileRightLongitude - $tileLeftLongitude);

        $point = new Point($x, $y);

        $tile = self::draw($tile, $point);

        return $tile;
    }

    protected static function draw(Tile $tile, PointInterface $point): Tile
    {
        try {
            $white = (new RGBPalette())->color('#FFFFFF');
            $red = (new RGBPalette())->color('#FF0000');
            $blue = (new RGBPalette())->color('#0000FF');

            try {
                $oldColor = Pipette::getColor($tile, $point);

                if ($oldColor !== $white) {
                    Brush::paint($tile, $point, $red);
                } else {
                    Brush::paint($tile, $point, $blue);
                }
            } catch (\RuntimeException $exception) {
                //Brush::paint($canvas, $point, $blue);
            }
        } catch (\InvalidArgumentException $exception) {

        }

        return $tile;
    }
}
