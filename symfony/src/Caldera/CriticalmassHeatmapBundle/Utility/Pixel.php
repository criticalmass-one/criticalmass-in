<?php
/**
 * Created by PhpStorm.
 * User: Malte
 * Date: 31.05.14
 * Time: 17:23
 */

namespace Caldera\CriticalmassHeatmapBundle\Utility;


class Pixel {
    protected $x;
    protected $y;
    protected $value;

    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;

        $this->value = rand(0, 4);
    }

    public function inc()
    {
        ++$this->value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getX()
    {
        return $this->x;
    }

    public function getY()
    {
        return $this->y;
    }
} 