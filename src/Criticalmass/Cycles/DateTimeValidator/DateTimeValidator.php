<?php declare(strict_types=1);

namespace App\Criticalmass\Cycles\DateTimeValidator;

use App\Entity\CityCycle;
use App\Entity\Ride;

class DateTimeValidator
{
    private function __construct()
    {

    }

    public static function isValidRide(CityCycle $cityCycle, Ride $ride): bool
    {
        return self::isValidDateTime($cityCycle, $ride->getDateTime());
    }

    public static function isValidDateTime(CityCycle $cityCycle, \DateTime $dateTime): bool
    {
        return ($cityCycle->getValidFrom() <= $dateTime && $cityCycle->getValidUntil() >= $dateTime) ||
            ($cityCycle->getValidFrom() <= $dateTime && $cityCycle->getValidUntil() === null) ||
            ($cityCycle->getValidFrom() === null && $cityCycle->getValidUntil() >= $dateTime);
    }
}