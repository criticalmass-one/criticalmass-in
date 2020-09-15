<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\TilePrinter;

use App\Criticalmass\Heatmap\Brush\Brush;
use App\Criticalmass\Heatmap\ColorStyle\ColorStyleInterface;
use App\Criticalmass\Heatmap\DimensionCalculator\TileDimensionCalculator;
use App\Criticalmass\Heatmap\Pipette\AveragePipette;
use App\Criticalmass\Heatmap\Tile\Tile;
use Caldera\GeoBasic\Coord\CoordInterface;
use Imagine\Image\Point;
use Imagine\Image\PointInterface;
use InvalidArgumentException;

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
        $tileDimension = TileDimensionCalculator::calculate($tile);

        $y = Tile::SIZE * ($tileDimension->getTileTopLatitude() - $coord->getLatitude()) / ($tileDimension->getTileTopLatitude() - $tileDimension->getTileBottomLatitude());
        $x = Tile::SIZE * ($coord->getLongitude() - $tileDimension->getTileLeftLongitude()) / ($tileDimension->getTileRightLongitude() - $tileDimension->getTileLeftLongitude());

        try {
            $point = new Point($x, $y);

            $tile = $this->draw($tile, $point);
        } catch (InvalidArgumentException $exception) {
        }

        return $tile;
    }

    protected function draw(Tile $tile, PointInterface $point): Tile
    {
        $oldColor = AveragePipette::getColor($tile, $point);

        $newColor = $this->colorStyle->colorize($oldColor);

        Brush::paint($tile, $point, $newColor);

        return $tile;
    }
}
