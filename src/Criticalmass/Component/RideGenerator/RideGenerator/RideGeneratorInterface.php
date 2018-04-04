<?php

namespace Criticalmass\Component\RideGenerator\RideGenerator;

use Criticalmass\Bundle\AppBundle\Entity\City;

interface RideGeneratorInterface
{
    public function addCity(City $city): RideGeneratorInterface;
    public function setCityList(array $cityList): RideGeneratorInterface;
    public function setYear(int $year): RideGeneratorInterface;
    public function setMonth(int $month): RideGeneratorInterface;
    public function getRideList(): array;
    public function execute(): RideGeneratorInterface;
}
