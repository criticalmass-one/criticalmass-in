<?php

namespace Criticalmass\Component\RideGenerator\RideGenerator;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

abstract class AbstractRideGenerator implements RideGeneratorInterface
{
    /** @var int year */
    protected $year;

    /** @var int $month */
    protected $month;

    /** @var \DateTime $endDateTime */
    protected $startDateTime;

    /** @var \DateTime $endDateTime */
    protected $endDateTime;

    /** @var City $city */
    protected $city;

    /** @var array $rideList */
    protected $rideList = [];

    /** @var Doctrine $doctrine */
    protected $doctrine;

    public function __construct(Doctrine $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function setCity(City $city): RideGeneratorInterface
    {
        $this->city = $city;

        return $this;
    }

    public function setYear(int $year): RideGeneratorInterface
    {
        $this->year = $year;

        return $this;
    }

    public function setMonth(int $month): RideGeneratorInterface
    {
        $this->month = $month;

        return $this;
    }

    public function getList(): array
    {
        return $this->rideList;
    }

    public abstract function execute(): RideGeneratorInterface;
}
