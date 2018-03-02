<?php

namespace Criticalmass\Component\RideGenerator\RideGenerator;

use Criticalmass\Bundle\AppBundle\Entity\City;

interface RideGeneratorInterface
{
    public function setCity(City $city): RideGeneratorInterface;

    public function setYear(int $year): RideGeneratorInterface;

    public function setMonth(int $month): RideGeneratorInterface;

    public function getList(): array;

    public function execute(): RideGeneratorInterface;
}
