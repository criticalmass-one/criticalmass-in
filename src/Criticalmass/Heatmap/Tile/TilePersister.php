<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Tile;

use App\Criticalmass\Heatmap\FilenameGenerator\FilenameGenerator;
use App\Criticalmass\Heatmap\HeatmapInterface;
use League\Flysystem\FilesystemInterface;

class TilePersister
{
    /** @var FilesystemInterface $filesystem */
    protected $filesystem;

    public function __construct(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function save(HeatmapInterface $heatmap, Tile $tile): bool
    {
        $filename = FilenameGenerator::generateForTile($heatmap, $tile);

        return $this->filesystem->put($filename, $tile->newImage()->get('png'));
    }
}