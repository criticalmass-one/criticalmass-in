<?php

namespace Criticalmass\Bundle\AppBundle\Utils;

class DateTimeUtils
{
    public static function getMonthStartDateTime(\DateTime $month): \DateTime
    {
        $dateTime = sprintf('%d-%d-01 00:00:00', $month->format('Y'), $month->format('m'));

        return new \DateTime($dateTime);
    }

    public static function getMonthEndDateTime(\DateTime $month): \DateTime
    {
        $dateTime = sprintf('%d-%d-%d 23:59:59', $month->format('Y'), $month->format('m'), $month->format('t'));

        return new \DateTime($dateTime);
    }

    public static function getDayStartDateTime(\DateTime $day): \DateTime
    {
        $dateTime = sprintf('%d-%d-01 00:00:00', $day->format('Y'), $day->format('m'));

        return new \DateTime($dateTime);
    }

    public static function getDayEndDateTime(\DateTime $day): \DateTime
    {
        $dateTime = sprintf('%d-%d-%d 23:59:59', $day->format('Y'), $day->format('m'), $day->format('d'));

        return new \DateTime($dateTime);
    }
}
