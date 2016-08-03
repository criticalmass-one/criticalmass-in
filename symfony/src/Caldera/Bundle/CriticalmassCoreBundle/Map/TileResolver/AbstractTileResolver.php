<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Map\TileResolver;

use Caldera\Bundle\CriticalmassCoreBundle\Gps\Coord;

abstract class AbstractTileResolver implements TileResolverInterface
{
    /** @var string $url */
    static protected $url;

    /** @var array $serverList */
    static protected $serverList = ['a', 'b', 'c'];

    public static function getByLatitudeLongitude($latitude, $longitude, $zoomLevel)
    {
        $coord = new Coord($latitude, $longitude);

        return self::getByCoord($coord, $zoomLevel);
    }

    protected static function getUrlWithServer($x, $y, $z)
    {
        $s = self::$serverList[array_rand(self::$serverList)];

        $search = ['{s}', '{x}', '{y}', '{z}'];
        $replace = [$s, $x, $y, $z];

        return str_replace($search, $replace, self::$url);
    }
}