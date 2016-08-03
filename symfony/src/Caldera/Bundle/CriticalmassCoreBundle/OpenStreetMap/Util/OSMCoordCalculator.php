<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\OpenStreetMap\Util;

class OsmCoordCalculator
{

    public static function osmYTileToLatitude($osmYTile, $zoom)
    {
        return rad2deg(atan(sinh(pi() * (1 - 2 * $osmYTile / pow(2, $zoom)))));
    }

    public static function osmXTileToLongitude($osmXTile, $zoom)
    {
        return $osmXTile / pow(2, $zoom) * 360.0 - 180.0;
    }

    public static function latitudeToOSMYTile($latitude, $zoom)
    {
        return floor((1 - log(tan(deg2rad($latitude)) + 1 / cos(deg2rad($latitude))) / pi()) /2 * pow(2, $zoom));
    }

    public static function longitudeToOSMXTile($longitude, $zoom)
    {
        return floor((($longitude + 180) / 360) * pow(2, $zoom));
    }
} 