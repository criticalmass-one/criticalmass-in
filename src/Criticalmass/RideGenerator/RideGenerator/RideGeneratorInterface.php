<?php declare(strict_types=1);

namespace App\Criticalmass\RideGenerator\RideGenerator;

use App\Entity\City;

interface RideGeneratorInterface
{
    public function setDateTimeList(array $dateTimeList): RideGeneratorInterface;
    
    public function setDateTime(\DateTime $dateTime): RideGeneratorInterface;

    public function addDateTime(\DateTime $dateTime): RideGeneratorInterface;

    public function addCity(City $city): RideGeneratorInterface;

    public function setCityList(array $cityList): RideGeneratorInterface;

    public function getRideList(): array;

    public function execute(): RideGeneratorInterface;
}
