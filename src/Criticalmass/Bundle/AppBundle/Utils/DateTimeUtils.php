<?php

namespace Criticalmass\Bundle\AppBundle\Utils;

class DateTimeUtils
{
    public static function getMonthStartDateTime(\DateTime $month)
    {
        $dateTime = sprintf('%d-%d-01 00:00:00', $month->format('Y'), $month->format('m'));

        return new \DateTime($dateTime);
    }

    public static function getMonthEndDateTime(\DateTime $month)
    {
        $monthDays = $month->format('t');
        $dateTime = sprintf('%d-%d-01 23:59:59', $month->format('Y'), $month->format('m'));

        return new \DateTime($dateTime);
    }

    public static function getDayStartDateTime(\DateTime $day)
    {
        $dateTime = sprintf('%d-%d-01 00:00:00', $day->format('Y'), $day->format('m'));

        return new \DateTime($dateTime);
    }

    public function getDayEndDateTime(\DateTime $day)
    {
        $dateTime = sprintf('%d-%d-%d 23:59:59', $day->format('Y'), $day->format('m'), $day->format('d'));

        return new \DateTime($dateTime);
    }
}
