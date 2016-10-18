<?php

namespace Caldera\CriticalmassStatisticBundle\Utility\Heatmap;


class Path
{
    protected $startPosition;
    protected $endPosition;

    public function __construct($startPosition, $endPosition)
    {
        $this->startPosition = $startPosition;
        $this->endPosition = $endPosition;
    }

    public function getStartPosition()
    {
        return $this->startPosition;
    }

    public function getEndPosition()
    {
        return $this->endPosition;
    }

    public function getHash()
    {
        return $this->startPosition->getLatitude() . "-" . $this->startPosition->getLongitude() . "-" . $this->endPosition->getLatitude() . "-" . $this->endPosition->getLongitude();
    }
} 