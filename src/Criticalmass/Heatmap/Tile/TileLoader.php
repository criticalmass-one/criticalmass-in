<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Tile;

use App\Criticalmass\Heatmap\FilenameGenerator\FilenameGenerator;
use App\Criticalmass\Heatmap\HeatmapInterface;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use League\Flysystem\FilesystemInterface;

class TileLoader
{
    /** @var FilesystemInterface $filesystem */
    protected $filesystem;

    public function __construct(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function load(HeatmapInterface $heatmap, int $tileX, int $tileY, int $zoomLevel): Tile
    {
        $tile = new Tile($tileX, $tileY, $zoomLevel);

        $filename = FilenameGenerator::generate($heatmap, $tile);

        if (!$this->filesystem->has($filename)) {
            $box = new Box(Tile::SIZE, Tile::SIZE);
            $image = (new Imagine())->create($box);
        } else {
            $image = (new Imagine())->read($this->filesystem->read($filename));
        }

        $tile->setImage($image);

        return $tile;
    }
}
