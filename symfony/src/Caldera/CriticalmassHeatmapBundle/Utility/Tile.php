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

    public function dropPath(Path $path)
    {
        $debug = false;
        if ($debug)
        {
            echo "=================<br />";
            echo "Groesse dieses Tiles:<br />";
            echo "TopLatitude: ".$this->getTopLatitude()."<br />";
            echo "BottomLatitude: ".$this->getBottomLatitude()."<br />";
            echo "LeftLongitude: ".$this->getLeftLongitude()."<br />";
            echo "RightLongitude: ".$this->getRightLongitude()."<br />";
            echo "=================<br /><br />";
        }

        $vector = array();

        $vector[0] = $path->getEndPosition()->getLatitude() - $path->getStartPosition()->getLatitude();
        $vector[1] = $path->getEndPosition()->getLongitude() - $path->getStartPosition()->getLongitude();

        if ($debug)
        {
            echo "=================<br />";
            echo "Positionen:<br />";
            echo "Latitude1: ".$path->getStartPosition()->getLatitude()."<br />";
            echo "Longitude1: ".$path->getStartPosition()->getLongitude()."<br />";
            echo "Latitude2: ".$path->getEndPosition()->getLatitude()."<br />";
            echo "Longitude2: ".$path->getEndPosition()->getLongitude()."<br />";
            echo "Vector: (".$vector[0].", ".$vector[1].")<br />";
            echo "=================<br /><br />";
        }

        $n = 100;

        for ($i = 0; $i < $n; ++$i)
        {
            $latitude = $path->getStartPosition()->getLatitude() + $i * $vector[0] * (1 / $n);
            $longitude = $path->getStartPosition()->getLongitude() + $i * $vector[1] * (1 / $n);

            $x = ($latitude - $this->getTopLatitude()) / ($this->getBottomLatitude() - $this->getTopLatitude());
            $y = ($longitude - $this->getLeftLongitude()) / ($this->getRightLongitude() - $this->getLeftLongitude());

            $x = round(256 / $x / 100);
            $y = round(256 / $y / 100);

            $this->addPixel(new Pixel($x, $y));
        }
    }

    public function getSize()
    {
        return $this->size;
    }

    protected function addPixel(Pixel $pixel)
    {
        $this->pixelList[] = $pixel;
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