<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\Converter;

use App\Criticalmass\Geo\Entity\Position;
use App\Criticalmass\Geo\EntityInterface\PositionInterface;

class GpxPointToPositionConverter
{
    private function __construct()
    {

    }

    public static function convert(\SimpleXMLElement $simpleXMLElement): ?PositionInterface
    {
        if (!$simpleXMLElement['lat'] || !$simpleXMLElement['lon']) {
            return null;
        }

        $latitude = (float) $simpleXMLElement['lat'];
        $longitude = (float) $simpleXMLElement['lon'];

        $position = new Position($latitude, $longitude);

        if ($simpleXMLElement->hdop) {
            $position->setAccuracy((float) $simpleXMLElement->hdop[0]);
        }

        if ($simpleXMLElement->ele) {
            $position->setAltitude((float) $simpleXMLElement->ele[0]);
        }

        if ($simpleXMLElement->vdop) {
            $position->setAltitudeAccuracy((float) $simpleXMLElement->vdop[0]);
        }

        if ($simpleXMLElement->time) {
            $position->setDateTime(new \DateTime((string) $simpleXMLElement->time));
        }

        if ($simpleXMLElement->extensions) {
            if ($simpleXMLElement->extensions->heading) {
                $position->setHeading((float) $simpleXMLElement->extensions->heading[0]);
            }

            if ($simpleXMLElement->extensions->speed) {
                $position->setSpeed((float) $simpleXMLElement->extensions->speed[0]);
            }
        }

        return $position;
    }
}