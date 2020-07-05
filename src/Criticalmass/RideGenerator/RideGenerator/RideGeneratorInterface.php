<?php declare(strict_types=1);

namespace App\Criticalmass\RideGenerator\RideGenerator;

interface RideGeneratorInterface
{
    public function setDateTimeList(array $dateTimeList): RideGeneratorInterface;

    public function setDateTime(\DateTime $dateTime): RideGeneratorInterface;

    public function addDateTime(\DateTime $dateTime): RideGeneratorInterface;

    public function getRideList(): array;

    public function execute(): RideGeneratorInterface;
}
