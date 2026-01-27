<?php declare(strict_types=1);

namespace App\Criticalmass\Util;

use Carbon\Carbon;

class DateTimeUtil
{
    public static function getYearStartDateTime(\DateTimeInterface $year): Carbon
    {
        return Carbon::instance($year)->startOfYear();
    }

    public static function getYearEndDateTime(\DateTimeInterface $year): Carbon
    {
        return Carbon::instance($year)->endOfYear();
    }

    public static function getMonthStartDateTime(\DateTimeInterface $month): Carbon
    {
        return Carbon::instance($month)->startOfMonth();
    }

    public static function getMonthEndDateTime(\DateTimeInterface $month): Carbon
    {
        return Carbon::instance($month)->endOfMonth();
    }

    public static function getDayStartDateTime(\DateTimeInterface $day): Carbon
    {
        return Carbon::instance($day)->startOfDay();
    }

    public static function getDayEndDateTime(\DateTimeInterface $day): Carbon
    {
        return Carbon::instance($day)->endOfDay();
    }

    public static function recreateAsUtc(\DateTimeInterface $dateTime): Carbon
    {
        return Carbon::parse($dateTime->format('Y-m-d H:i:s'), 'UTC');
    }

    public static function recreateAsTimeZone(\DateTimeInterface $dateTime, \DateTimeZone $dateTimeZone): Carbon
    {
        return Carbon::parse($dateTime->format('Y-m-d H:i:s'), $dateTimeZone);
    }
}
