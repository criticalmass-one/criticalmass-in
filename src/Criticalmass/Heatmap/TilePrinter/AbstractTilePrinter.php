<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\TilePrinter;

use App\Criticalmass\Heatmap\Tile\Tile;

abstract class AbstractTilePrinter
{
    /** @var Tile $tile */
    protected $tile;

    public function __construct(Tile $tile)
    {
        $this->tile = $tile;
    }

    public abstract function printTile();
}