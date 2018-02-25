<?php

namespace Criticalmass\Component\RideGenerator\RideCalculator;

use Criticalmass\Bundle\AppBundle\Entity\CityCycle;

interface RideCalculatorInterface
{
    public function setCycleList(array $cycleList): RideCalculatorInterface;
    public function addCycle(CityCycle $cityCycle): RideCalculatorInterface;
    public function setYear(int $year): RideCalculatorInterface;
    public function setMonth(int $month): RideCalculatorInterface;
    public function setDateTime(\DateTime $dateTime): RideCalculatorInterface;
    public function getRideList(): array;
    public function execute(): RideCalculatorInterface;
}
