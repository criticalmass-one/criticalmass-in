<?php
/**
 * Created by PhpStorm.
 * User: Malte
 * Date: 31.05.14
 * Time: 17:37
 */

namespace Caldera\CriticalmassHeatmapBundle\Utility;


class Position {
    protected $latitude;
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