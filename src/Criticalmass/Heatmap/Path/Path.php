<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Path;

use Caldera\GeoBasic\Coord\CoordInterface;

class Path
{
    public function __construct(protected CoordInterface $startCoord, protected CoordInterface $endCoord)
    {
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
