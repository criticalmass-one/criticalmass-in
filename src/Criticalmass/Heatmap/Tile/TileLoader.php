<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Tile;

use App\Criticalmass\Heatmap\FilenameGenerator\FilenameGenerator;
use App\Criticalmass\Heatmap\HeatmapInterface;
use Imagine\Gd\Image;
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

    /*
    public function load(HeatmapInterface $heatmap, int $tileX, int $tileY, int $zoomLevel): Tile
    {
        $tile = new Tile($tileX, $tileY, $zoomLevel);

        $filename = FilenameGenerator::generate($heatmap, $tile);

        if (!$this->filesystem->has($filename)) {
            $box = new Box(256, 256);
            $image = (new Imagine())->create($box);
        } else {
            $image = (new Imagine())->read($this->filesystem->read($filename));
        }

        $tile->setImage($image);

        return $tile;
    }*/

    public function load(HeatmapInterface $heatmap, int $tileX, int $tileY, int $zoomLevel): Tile
    {
        $tile = new Tile($tileX, $tileY, $zoomLevel);

        $filename = sprintf('https://tiles.caldera.cc/wikimedia-intl/%d/%d/%d.png', $zoomLevel, $tileX, $tileY);

        $image = (new Imagine())->load($filename);

        $tile->setImage($image);

        return $tile;
    }
}