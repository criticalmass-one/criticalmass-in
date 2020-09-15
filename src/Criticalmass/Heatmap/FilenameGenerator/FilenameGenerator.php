<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\FilenameGenerator;

use App\Criticalmass\Heatmap\HeatmapInterface;
use App\Criticalmass\Heatmap\Tile\Tile;

class FilenameGenerator
{
    public static function generateForTile(HeatmapInterface $heatmap, Tile $tile): string
    {
        return self::generateForXYZ($heatmap, $tile->getTileX(), $tile->getTileY(), $tile->getZoomLevel());
    }

    public static function generateForXYZ(HeatmapInterface $heatmap, int $tileX, int $tileY, int $zoomLevel): string
    {
        return sprintf('%s/%d/%d/%d.png',
            $heatmap->getIdentifier(),
            $zoomLevel,
            $tileX,
            $tileY
        );
    }
}