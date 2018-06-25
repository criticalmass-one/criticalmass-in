<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\RideGenerator\RideCalculator;

use AppBundle\Entity\CityCycle;

abstract class AbstractRideCalculator implements RideCalculatorInterface
{
    /** @var int year */
    protected $year;

    /** @var int $month */
    protected $month;

    /** @var array $cycle */
    protected $cycleList;

    /** @var array $rideList */
    protected $rideList;

    /** @var \DateTimeZone $timezone */
    protected $timezone = null;

    public function __construct()
    {
    }

    public function setTimezone(\DateTimeZone $timezone): RideCalculatorInterface
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function setYear(int $year): RideCalculatorInterface
    {
        $this->year = $year;

        return $this;
    }

    public function setMonth(int $month): RideCalculatorInterface
    {
        $this->month = $month;

        return $this;
    }

    public function setDateTime(\DateTime $dateTime): RideCalculatorInterface
    {
        $this->year = $dateTime->format('Y');
        $this->month = $dateTime->format('m');

        return $this;
    }

    public function setCycleList(array $cycleList): RideCalculatorInterface
    {
        $this->cycleList = $cycleList;

        return $this;
    }

    public function addCycle(CityCycle $cityCycle): RideCalculatorInterface
    {
        $this->cycleList[] = $cityCycle;

        return $this;
    }

    public abstract function execute(): RideCalculatorInterface;

    public function getRideList(): array
    {
        return $this->rideList;
    }

    public function reset(): RideCalculatorInterface
    {
        $this->cycleList = [];
        $this->rideList = [];

        return $this;
    }
}
