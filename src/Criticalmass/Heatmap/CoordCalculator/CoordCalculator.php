<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\CoordCalculator;

class CoordCalculator
{
    private function __construct()
    {

    }

    public static function yTileToLatitude(int $osmYTile, int $zoom): float
    {
        return rad2deg(atan(sinh(pi() * (1 - 2 * $osmYTile / pow(2, $zoom)))));
    }

    public static function xTileToLongitude(int $osmXTile, int $zoom): float
    {
        return $osmXTile / pow(2, $zoom) * 360.0 - 180.0;
    }

    public static function latitudeToYTile($latitude, $zoom): int
    {
        return (int) floor((1 - log(tan(deg2rad($latitude)) + 1 / cos(deg2rad($latitude))) / pi()) /2 * pow(2, $zoom));
    }

    public static function longitudeToXTile($longitude, $zoom): int
    {
        return (int) floor((($longitude + 180) / 360) * pow(2, $zoom));
    }
}