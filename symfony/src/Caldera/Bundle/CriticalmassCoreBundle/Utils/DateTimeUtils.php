<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Utils;

class DateTimeUtils
{
    public static function getMonthStartDateTime(\DateTime $month)
    {
        return new \DateTime($month->format('Y') . '-' . $month->format('m') . '-01 00:00:00');
    }

    public static function getMonthEndDateTime(\DateTime $month)
    {
        $monthDays = $month->format('t');

        return new \DateTime($month->format('Y') . '-' . $month->format('m') . '-' . $monthDays . ' 23:59:59');
    }

    public static function getDayStartDateTime(\DateTime $day)
    {
        return new \DateTime($day->format('Y') . '-' . $day->format('m') . '-' . $day->format('d') . ' 00:00:00');
    }

    public function getDayEndDateTime(\DateTime $day)
    {
        return new \DateTime($day->format('Y') . '-' . $day->format('m') . '-' . $day->format('d') . ' 23:59:59');
    }
}