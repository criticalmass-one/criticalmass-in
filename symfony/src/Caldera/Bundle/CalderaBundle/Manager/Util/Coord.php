<?php
/**
 * Created by PhpStorm.
 * User: maltehuebner
 * Date: 08.11.16
 * Time: 20:09
 */

namespace Caldera\Bundle\CalderaBundle\Manager\Util;


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
}