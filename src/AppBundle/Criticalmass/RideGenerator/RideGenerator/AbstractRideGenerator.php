<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\RideGenerator\RideGenerator;

use AppBundle\Entity\City;
use AppBundle\Criticalmass\RideGenerator\RideCalculator\RideCalculatorInterface;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

abstract class AbstractRideGenerator implements RideGeneratorInterface
{
    /** @var int year */
    protected $year;

    /** @var int $month */
    protected $month;

    /** @var array $cityList */
    protected $cityList;

    /** @var array $rideList */
    protected $rideList = [];

    /** @var Doctrine $doctrine */
    protected $doctrine;

    /** @var RideCalculatorInterface $rideCalculator */
    protected $rideCalculator;

    public function __construct(Doctrine $doctrine, RideCalculatorInterface $rideCalculator)
    {
        $this->doctrine = $doctrine;
        $this->rideCalculator = $rideCalculator;
    }

    public function addCity(City $city): RideGeneratorInterface
    {
        $this->cityList[] = $city;

        return $this;
    }

    public function setCityList(array $cityList): RideGeneratorInterface
    {
        $this->cityList = $cityList;

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

    public function getRideList(): array
    {
        return $this->rideList;
    }

    public abstract function execute(): RideGeneratorInterface;
}
