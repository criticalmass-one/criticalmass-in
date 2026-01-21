<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\Coord;

class Coord implements CoordInterface
{
    public function __construct(
        protected ?float $latitude = null,
        protected ?float $longitude = null
    )
    {

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
