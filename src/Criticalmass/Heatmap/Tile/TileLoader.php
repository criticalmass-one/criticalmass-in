<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Tile;

use App\Criticalmass\Heatmap\FilenameGenerator\FilenameGenerator;
use App\Criticalmass\Heatmap\HeatmapInterface;
use Imagine\Gd\Imagine;
use League\Flysystem\FilesystemInterface;

class TileLoader
{
    public function __construct(protected TileFactory $tileFactory, protected FilesystemInterface $filesystem)
    {
    }

    public function load(HeatmapInterface $heatmap, int $tileX, int $tileY, int $zoomLevel): Tile
    {
        $filename = FilenameGenerator::generateForXYZ($heatmap, $tileX, $tileY, $zoomLevel);

        if (!$this->filesystem->has($filename)) {
            $image = null;
        } else {
            $image = (new Imagine())->load($this->filesystem->read($filename));
        }

        return $this->tileFactory->create($tileX, $tileY, $zoomLevel, $image);
    }
}
