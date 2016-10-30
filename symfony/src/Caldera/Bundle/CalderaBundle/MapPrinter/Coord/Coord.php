<?php

namespace Caldera\Bundle\CalderaBundle\MapPrinter\Coord;

class Coord
{
    /** @var float $latitude */
    protected $latitude = null;

    /** @var float $longitude */
    protected $longitude = null;

    public function __construct(float $latitude = null, float $longitude = null)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function toArray(): array
    {
        return [
            $this->latitude,
            $this->longitude
        ];
    }

    public function toInversedArray(): array
    {
        return [
            $this->longitude,
            $this->latitude
        ];
    }

    public function setLatitude(float $latitude): Coord
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function setLongitude(float $longitude): Coord
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function northOf(Coord $coord): bool
    {
        return $this->latitude > $coord->getLatitude();
    }

    public function southOf(Coord $coord): bool
    {
        return $this->latitude < $coord->getLatitude();
    }

    public function westOf(Coord $coord): bool
    {
        return $this->longitude < $coord->getLongitude();
    }

    public function eastOf(Coord $coord): bool
    {
        return $this->longitude > $coord->getLongitude();
    }
}