<?php

namespace Caldera\Bundle\CalderaBundle\MapPrinter\Util;

class OsmZxyCalculator
{
    public static function osmYTileToLatitude($osmYTile, $zoomLevel): float
    {
        return rad2deg(atan(sinh(pi() * (1 - 2 * $osmYTile / pow(2, $zoomLevel)))));
    }

    public static function osmXTileToLongitude(int $osmXTile, int $zoomLevel): float
    {
        return $osmXTile / pow(2, $zoomLevel) * 360.0 - 180.0;
    }

    public static function latitudeToOSMYTile(float $latitude, int $zoomLevel): int
    {
        return (int)floor((1 - log(tan(deg2rad($latitude)) + 1 / cos(deg2rad($latitude))) / pi()) / 2 * pow(2, $zoomLevel));
    }

    public static function longitudeToOSMXTile(float $longitude, int $zoomLevel): int
    {
        return (int)floor((($longitude + 180) / 360) * pow(2, $zoomLevel));
    }
} 