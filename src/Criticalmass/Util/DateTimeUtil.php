<?php declare(strict_types=1);

namespace App\Criticalmass\Util;

use Carbon\Carbon;

class DateTimeUtil
{
    public static function getYearStartDateTime(Carbon $year): Carbon
    {
        return $year->copy()->startOfYear();
    }

    public static function getYearEndDateTime(Carbon $year): Carbon
    {
        return $year->copy()->endOfYear();
    }

    public static function getMonthStartDateTime(Carbon $month): Carbon
    {
        return $month->copy()->startOfMonth();
    }

    public static function getMonthEndDateTime(Carbon $month): Carbon
    {
        return $month->copy()->endOfMonth();
    }

    public static function getDayStartDateTime(Carbon $day): Carbon
    {
        return $day->copy()->startOfDay();
    }

    public static function getDayEndDateTime(Carbon $day): Carbon
    {
        return $day->copy()->endOfDay();
    }

    public static function recreateAsUtc(Carbon $dateTime): Carbon
    {
        return Carbon::create($dateTime->format('Y-m-d H:i:s'), 'UTC');
    }

    public static function recreateAsTimeZone(Carbon $dateTime, \DateTimeZone $dateTimeZone): Carbon
    {
        return Carbon::create($dateTime->format('Y-m-d H:i:s'), $dateTimeZone);
    }
}
