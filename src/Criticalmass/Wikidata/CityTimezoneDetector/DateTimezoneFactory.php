<?php declare(strict_types=1);

namespace App\Criticalmass\Wikidata\CityTimezoneDetector;

use Wikidata\Property;

class DateTimezoneFactory
{
    private function __construct()
    {

    }

    public static function createFromWikidataProperty(Property $timezoneProperty): \DateTimeZone
    {
        $timezones = explode(' ', $timezoneProperty->value);

        $timezone = new \DateTimeZone(str_replace(['UTC+', ':'], ['+', ''], array_pop($timezones)));

        return $timezone;
    }
}