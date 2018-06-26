<?php

namespace AppBundle\Criticalmass\Gps;

/**
 * @deprecated
 */
class Coord
{
    /** @var float $latitude */
    protected $latitude;

    /** @var float $longitude */
    protected $longitude;

    public function __construct($latitude, $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }
}
