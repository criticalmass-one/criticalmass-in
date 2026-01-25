<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\GeoUtil;

use App\Criticalmass\Geo\Coord\CoordInterface;

/**
 * Utility class for geographic calculations.
 */
class GeoUtil
{
    /**
     * Simplified distance calculation between two coordinates.
     * Returns distance in kilometers.
     *
     * This uses a simplified formula that works well for short distances
     * in middle latitudes but is not accurate for very long distances.
     */
    public static function calculateDistance(CoordInterface $coordA, CoordInterface $coordB): float
    {
        $dx = 71.5 * ($coordA->getLongitude() - $coordB->getLongitude());
        $dy = 111.3 * ($coordA->getLatitude() - $coordB->getLatitude());

        return sqrt($dx * $dx + $dy * $dy);
    }

    /**
     * Calculate distance between two points using their lat/lng values directly.
     */
    public static function calculateDistanceFromCoords(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $dx = 71.5 * ($lon1 - $lon2);
        $dy = 111.3 * ($lat1 - $lat2);

        return sqrt($dx * $dx + $dy * $dy);
    }
}
