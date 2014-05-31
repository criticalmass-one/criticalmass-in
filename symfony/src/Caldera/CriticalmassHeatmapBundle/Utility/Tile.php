<?php

namespace Caldera\CriticalmassHeatmapBundle\Utility;


class Tile {
    protected $pixelList;
    protected $size = 256;

    protected $latitude;
    protected $longitude;

    protected $osmXTile;
    protected $osmYTile;
    protected $osmZoom;

    public function __construct()
    {

    }

    public function generatePlaceByLatitudeLongitudeZoom($latitude, $longitude, $zoom)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;

        $this->osmXTile = floor((($longitude + 180) / 360) * pow(2, $zoom));
        $this->osmYTile = floor((1 - log(tan(deg2rad($latitude)) + 1 / cos(deg2rad($latitude))) / pi()) /2 * pow(2, $zoom));
        $this->osmZoom = $zoom;
    }

    public function getOsmXTile()
    {
        return $this->osmXTile;
    }

    public function getOsmYTile()
    {
        return $this->osmYTile;
    }

    public function getOsmZoom()
    {
        return $this->osmZoom;
    }

    public function getTopLatitude()
    {
        return rad2deg(atan(sinh(pi() * (1 - 2 * $this->osmYTile / pow(2, $this->osmZoom)))));
    }

    public function getBottomLatitude()
    {
        return rad2deg(atan(sinh(pi() * (1 - 2 * ($this->osmYTile + 1)/ pow(2, $this->osmZoom)))));
    }

    public function getLeftLongitude()
    {
        return $this->osmXTile / pow(2, $this->osmZoom) * 360.0 - 180.0;
    }

    public function getRightLongitude()
    {
        return ($this->osmXTile + 1) / pow(2, $this->osmZoom) * 360.0 - 180.0;
    }

    public function dropPathArray($pathArray)
    {
        foreach ($pathArray as $path)
        {
            $this->dropPath($path);
        }
    }

    public function dropPath(Path $path)
    {
        $vector = array();

        $vector[0] = (float) $path->getEndPosition()->getLatitude() - $path->getStartPosition()->getLatitude();
        $vector[1] = (float) $path->getEndPosition()->getLongitude() - $path->getStartPosition()->getLongitude();

        $n = 5;

        for ($i = 0; $i < $n; ++$i)
        {
            $latitude = (float) $path->getStartPosition()->getLatitude() + (float) $i * $vector[0] * (1 / $n);
            $longitude = (float) $path->getStartPosition()->getLongitude() + (float) $i * $vector[1] * (1.0 / $n);

            $x = $this->size / ($this->getRightLongitude() - $this->getLeftLongitude()) * ($longitude - $this->getLeftLongitude());
            $y = $this->size / ($this->getBottomLatitude() - $this->getTopLatitude()) * ($latitude - $this->getTopLatitude());

            if (($x >= 0) and ($x < $this->size) and ($y >= 0) and ($y < $this->size))
            {
                $this->addPixel(new Pixel(round($x), round($y)));
            }
        }
    }

    public function getSize()
    {
        return $this->size;
    }

    public function addPixel(Pixel $pixel)
    {
        $this->pixelList[$pixel->getHash()] = $pixel;
    }

    public function sortPixelList()
    {
        rsort($this->pixelList);
    }

    public function popPixel()
    {
        if (count($this->pixelList) > 0)
        {
            return array_pop($this->pixelList);
        }

        return null;
    }
}