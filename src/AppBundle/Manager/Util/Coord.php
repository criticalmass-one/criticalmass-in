<?php

namespace AppBundle\Manager\Util;

/**
 * @deprecated
 */
class Coord
{
    /** @var float $latitude */
    protected $latitude;

    /** @var float $longitude */
    protected $longitude;

    public function __construct(float $latitude, float $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function toArray(): array
    {
        return [
            $this->latitude,
            $this->longitude
        ];
    }

    public function toLatLonArray(): array
    {
        return [
            'lat' => $this->latitude,
            'lon' => $this->longitude
        ];
    }
}
