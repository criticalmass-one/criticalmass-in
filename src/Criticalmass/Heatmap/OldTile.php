<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap;

class OldTile
{
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

        $this->osmXTile = OSMCoordCalculator::longitudeToOSMXTile($longitude, $zoom);
        $this->osmYTile = OSMCoordCalculator::latitudeToOSMYTile($latitude, $zoom);
        $this->osmZoom = $zoom;
    }

    public function generatePlaceByTileXTileYZoom($tileX, $tileY, $zoom)
    {
        $this->osmXTile = $tileX;
        $this->osmYTile = $tileY;

        $this->latitude = OSMCoordCalculator::osmYTileToLatitude($tileX, $zoom);
        $this->longitude = OSMCoordCalculator::osmXTileToLongitude($tileY, $zoom);

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

    public function dropPathArray($pathArray)
    {
        foreach ($pathArray as $path) {
            $this->dropPath($path);
        }
    }

    public function dropPath(Path $path)
    {
        $vector = array();

        $vector[0] = (float)$path->getEndPosition()->getLatitude() - $path->getStartPosition()->getLatitude();
        $vector[1] = (float)$path->getEndPosition()->getLongitude() - $path->getStartPosition()->getLongitude();

        $n = 25;

        for ($i = 0; $i < $n; ++$i) {
            $latitude = (float)$path->getStartPosition()->getLatitude() + (float)$i * $vector[0] * (1 / $n);
            $longitude = (float)$path->getStartPosition()->getLongitude() + (float)$i * $vector[1] * (1 / $n);

            $x = (float)$this->size / ($this->getRightLongitude() - $this->getLeftLongitude()) * ($longitude - $this->getLeftLongitude());
            $y = (float)$this->size / ($this->getBottomLatitude() - $this->getTopLatitude()) * ($latitude - $this->getTopLatitude());

            if (($x >= 0) && ($x < $this->size) && ($y >= 0) && ($y < $this->size)) {
                $this->addPixel(new Pixel(floor($x), floor($y)));
            }
        }
    }

    public function getRightLongitude()
    {
        return OSMCoordCalculator::osmXTileToLongitude($this->osmXTile + 1, $this->osmZoom);
    }

    public function getLeftLongitude()
    {
        return OSMCoordCalculator::osmXTileToLongitude($this->osmXTile, $this->osmZoom);
    }

    public function getBottomLatitude()
    {
        return OSMCoordCalculator::osmYTileToLatitude($this->osmYTile + 1, $this->osmZoom);
    }

    public function getTopLatitude()
    {
        return OSMCoordCalculator::osmYTileToLatitude($this->osmYTile, $this->osmZoom);
    }

    public function addPixel(Pixel $pixel)
    {
        $this->pixelList[$pixel->getHash()] = $pixel;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function sortPixelList()
    {
        rsort($this->pixelList);
    }

    public function popPixel()
    {
        if ($this->hasPixel()) {
            return array_pop($this->pixelList);
        }

        return null;
    }

    public function hasPixel()
    {
        return $this->countPixel() > 0;
    }

    public function countPixel()
    {
        return count($this->pixelList);
    }
}