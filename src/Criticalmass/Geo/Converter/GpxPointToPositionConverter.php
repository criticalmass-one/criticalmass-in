<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\Converter;

use App\Criticalmass\Geo\Entity\Position;
use App\Criticalmass\Geo\EntityInterface\PositionInterface;

class GpxPointToPositionConverter
{
    private function __construct()
    {

    }

    public static function convert(\SimpleXMLElement $simpleXMLElement): PositionInterface
    {
        $latitude = (float) $simpleXMLElement['lat'];
        $longitude = (float) $simpleXMLElement['lon'];

        return new Position($latitude, $longitude);
    }
}