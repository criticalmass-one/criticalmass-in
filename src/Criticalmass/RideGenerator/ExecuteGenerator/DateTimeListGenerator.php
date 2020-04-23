<?php declare(strict_types=1);

namespace App\Criticalmass\RideGenerator\ExecuteGenerator;

class DateTimeListGenerator
{
    private function __construct()
    {

    }

    public static function generateDateTimeList(CycleExecutable $cycleExecutable): array
    {
        $dateTimeList = [];

        $dateTime = clone $cycleExecutable->getFromDate();
        $interval = new \DateInterval('P1M');

        while ($dateTime <= $cycleExecutable->getUntilDate()) {
            $dateTimeList[] = clone $dateTime;

            $dateTime->add($interval);
        }

        return $dateTimeList;
    }
}
