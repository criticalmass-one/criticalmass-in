<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\Coord;

class Coord implements CoordInterface
{
    protected ?float $latitude = null;
    protected ?float $longitude = null;

    public function __construct(?float $latitude = null, ?float $longitude = null)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }
}
