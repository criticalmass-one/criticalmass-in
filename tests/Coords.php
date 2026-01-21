<?php declare(strict_types=1);

namespace Tests;

use App\Geo\Coord\Coord;
use App\Geo\Coord\CoordInterface;

class Coords
{
    private function __construct()
    {
        
    }

    public static function hamburg(): CoordInterface
    {
        return new Coord(53.566676, 9.984711);
    }

    public static function halle(): CoordInterface
    {
        return new Coord(51.491819, 11.968641);
    }

    public static function berlin(): CoordInterface
    {
        return new Coord(52.500472, 13.423083);
    }

    public static function mainz(): CoordInterface
    {
        return new Coord(50.001452, 8.276696);
    }

    public static function london(): CoordInterface
    {
        return new Coord(51.507620, -0.114708);
    }

    public static function esslingen(): CoordInterface
    {
        return new Coord(48.739864, 9.307180);
    }

    public static function buedelsdorf(): CoordInterface
    {
        return new Coord(54.318072, 9.696301);
    }
}
