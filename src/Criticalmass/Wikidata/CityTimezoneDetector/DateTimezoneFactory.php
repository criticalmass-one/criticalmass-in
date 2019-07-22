<?php declare(strict_types=1);

namespace App\Criticalmass\Wikidata\CityTimezoneDetector;

class DateTimezoneFactory
{
    private function __construct()
    {

    }

    public static function createFromOffset(float $offset): \DateTimeZone
    {
        if ($offset >= 0) {
            $spec = sprintf('+%02d00', $offset);
        } else {
            $spec = sprintf('-%02d00', abs($offset));
        }

        return new \DateTimeZone($spec);
    }
}