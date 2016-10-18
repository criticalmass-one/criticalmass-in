<?php

namespace Caldera\CriticalmassStatisticBundle\Utility\Heatmap;


class OSMMapDimensionCalculator
{
    protected $pathArray;
    protected $zoom;

    protected $topTile;
    protected $bottomTile;
    protected $leftTile;
    protected $rightTile;

    public function __construct($pathArray, $zoom)
    {
        $this->pathArray = $pathArray;
        $this->zoom = $zoom;

        $path = array_shift($this->pathArray);

        $minLat = $path->getStartPosition()->getLatitude();
        $minLon = $path->getStartPosition()->getLongitude();
        $maxLat = $path->getStartPosition()->getLatitude();
        $maxLon = $path->getStartPosition()->getLongitude();

        while ($path != null) {
            if ($path->getEndPosition()->getLatitude() < $minLat) {
                $minLat = $path->getEndPosition()->getLatitude();
            }

            if ($path->getEndPosition()->getLongitude() < $minLon) {
                $minLon = $path->getEndPosition()->getLongitude();
            }

            if ($path->getEndPosition()->getLatitude() > $maxLat) {
                $maxLat = $path->getEndPosition()->getLatitude();
            }

            if ($path->getEndPosition()->getLongitude() > $maxLon) {
                $maxLon = $path->getEndPosition()->getLongitude();
            }

            $path = array_shift($this->pathArray);
        }

        $this->topTile = OSMCoordCalculator::latitudeToOSMYTile($minLat, $this->zoom);
        $this->bottomTile = OSMCoordCalculator::latitudeToOSMYTile($maxLat, $this->zoom);
        $this->leftTile = OSMCoordCalculator::longitudeToOSMXTile($minLon, $this->zoom);
        $this->rightTile = OSMCoordCalculator::longitudeToOSMXTile($maxLon, $this->zoom);
    }

    public function getTopTile()
    {
        return $this->topTile;
    }

    public function getBottomTile()
    {
        return $this->bottomTile;
    }

    public function getLeftTile()
    {
        return $this->leftTile;
    }

    public function getRightTile()
    {
        return $this->rightTile;
    }

} 