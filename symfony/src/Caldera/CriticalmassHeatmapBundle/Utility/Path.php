<?php
/**
 * Created by PhpStorm.
 * User: Malte
 * Date: 31.05.14
 * Time: 16:24
 */

namespace Caldera\CriticalmassHeatmapBundle\Utility;


class Path {
    protected $startPosition;
    protected $endPosition;

    public function Path($startPosition, $endPosition)
    {
        $this->startPosition = $startPosition;
        $this->endPosition = $endPosition;
    }
} 