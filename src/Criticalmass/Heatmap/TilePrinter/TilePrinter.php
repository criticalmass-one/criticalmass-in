<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\TilePrinter;

use App\Criticalmass\Heatmap\Brush\Brush;
use App\Criticalmass\Heatmap\ColorStyle\ColorStyleInterface;
use App\Criticalmass\Heatmap\CoordCalculator\CoordCalculator;
use App\Criticalmass\Heatmap\Pipette\AveragePipette;
use App\Criticalmass\Heatmap\Tile\Tile;
use Caldera\GeoBasic\Coord\CoordInterface;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;

class TilePrinter
{
    /** @var ColorStyleInterface $colorStyle */
    protected $colorStyle;

    public function __construct(ColorStyleInterface $colorStyle)
    {
        $this->colorStyle = $colorStyle;
    }

    public function printTile(Tile $tile, CoordInterface $coord): Tile
    {
        $tileTopLatitude = CoordCalculator::yTileToLatitude($tile->getTileY(), $tile->getZoomLevel());
        $tileLeftLongitude = CoordCalculator::xTileToLongitude($tile->getTileX(), $tile->getZoomLevel());
        $tileBottomLatitude = CoordCalculator::yTileToLatitude($tile->getTileY() + 1, $tile->getZoomLevel());
        $tileRightLongitude = CoordCalculator::xTileToLongitude($tile->getTileX() + 1, $tile->getZoomLevel());

        $y = Tile::SIZE * ($tileTopLatitude - $coord->getLatitude()) / ($tileTopLatitude - $tileBottomLatitude);
        $x = Tile::SIZE * ($coord->getLongitude() - $tileLeftLongitude) / ($tileRightLongitude - $tileLeftLongitude);

        $point = new Point($x, $y);

        $tile = $this->draw($tile, $point);

        return $tile;
    }

    protected function draw(Tile $tile, PointInterface $point): Tile
    {
        try {
            $oldColor = AveragePipette::getColor($tile, $point);

            $newColor = $this->colorStyle->colorize($oldColor);

            Brush::paint($tile, $point, $newColor);
        } catch (\InvalidArgumentException $exception) {

        }

        return $tile;
    }
}
