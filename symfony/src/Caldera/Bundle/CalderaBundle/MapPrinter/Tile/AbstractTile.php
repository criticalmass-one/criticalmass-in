<?php

namespace Caldera\Bundle\CalderaBundle\MapPrinter\Tile;

use Caldera\Bundle\CalderaBundle\MapPrinter\Coord\Coord;

class AbstractTile
{
    /** @var int $pixelWidth */
    protected $pixelWidth;
    
    /** @var int $pixelHeight */
    protected $pixelHeight;

    /** @var Coord $topLeft */
    protected $topLeft;

    /** @var Coord $bottomRight */
    protected $bottomRight;
}