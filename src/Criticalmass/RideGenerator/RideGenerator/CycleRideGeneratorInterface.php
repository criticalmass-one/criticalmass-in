<?php declare(strict_types=1);

namespace App\Criticalmass\RideGenerator\RideGenerator;

use App\Entity\CityCycle;

interface CycleRideGeneratorInterface extends RideGeneratorInterface
{
    public function addCycle(CityCycle $cityCycle): RideGeneratorInterface;

    public function setCycleList(array $cycleList): RideGeneratorInterface;
}