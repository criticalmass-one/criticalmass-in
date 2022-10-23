<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Tile;

use App\Criticalmass\Heatmap\FilenameGenerator\FilenameGenerator;
use App\Criticalmass\Heatmap\HeatmapInterface;
use League\Flysystem\FilesystemInterface;

class TilePersister
{
    public function __construct(protected FilesystemInterface $filesystem)
    {
    }

    public function save(HeatmapInterface $heatmap, Tile $tile): bool
    {
        $filename = FilenameGenerator::generateForTile($heatmap, $tile);

        return $this->filesystem->put($filename, $tile->newImage()->get('png'));
    }
}