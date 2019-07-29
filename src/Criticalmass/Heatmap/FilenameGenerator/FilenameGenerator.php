<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\FilenameGenerator;

use App\Criticalmass\Heatmap\HeatmapInterface;
use App\Criticalmass\Heatmap\Tile\Tile;

class FilenameGenerator
{
    public static function generate(HeatmapInterface $heatmap, Tile $tile): string
    {
        return sprintf('%s/%d/%d/%d.png',
            $heatmap->getIdentifier(),
            $tile->getZoomLevel(),
            $tile->getTileX(),
            $tile->getTileY(),
        );
    }
}