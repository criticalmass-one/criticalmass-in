<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Path;

use Caldera\GeoBasic\Coord\CoordInterface;

class Path
{
    /** @var CoordInterface $startCoord */
    protected $startCoord;

    /** @var CoordInterface $endCoord */
    protected $endCoord;

    public function __construct(CoordInterface $startCoord, CoordInterface $endCoord)
    {
        $this->startCoord = $startCoord;
        $this->endCoord = $endCoord;
    }

    public function getStartCoord(): CoordInterface
    {
        return $this->startCoord;
    }

    public function getEndCoord(): CoordInterface
    {
        return $this->endCoord;
    }

    public function getHash(): string
    {
        return sprintf('%f-%f-%f-%f', $this->startCoord->getLatitude(), $this->startCoord->getLongitude(), $this->endCoord->getLatitude(), $this->endCoord->getLongitude());
    }
}
