<?php declare(strict_types=1);

namespace App\Criticalmass\RideGenerator\RideCalculator;

use App\Entity\CityCycle;

interface RideCalculatorInterface
{
    public function setTimezone(\DateTimeZone $timezone): RideCalculatorInterface;
    public function setCycleList(array $cycleList): RideCalculatorInterface;
    public function addCycle(CityCycle $cityCycle): RideCalculatorInterface;
    public function setYear(int $year): RideCalculatorInterface;
    public function setMonth(int $month): RideCalculatorInterface;
    public function setDateTime(\DateTime $dateTime): RideCalculatorInterface;
    public function getRideList(): array;
    public function execute(): RideCalculatorInterface;
    public function reset(): RideCalculatorInterface;
}
