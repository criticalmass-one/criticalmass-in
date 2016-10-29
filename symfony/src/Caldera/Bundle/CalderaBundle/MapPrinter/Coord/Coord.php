<?php

namespace Caldera\Bundle\CalderaBundle\MapPrinter\Coord;

class Coord
{
    /** @var float $latitude */
    protected $latitude = null;

    /** @var float $longitude */
    protected $longitude = null;

    public function __construct(float $latitude = null, float $longitude = null): Coord
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }
}