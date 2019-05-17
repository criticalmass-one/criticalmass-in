<?php declare(strict_types=1);

namespace App\Criticalmass\RideGenerator\RideCalculator;

use App\Criticalmass\RideNamer\RideNamerListInterface;
use App\Entity\CityCycle;

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

    /** @var RideNamerListInterface $rideNamerList */
    protected $rideNamerList;

    /** @var \DateTimeZone $timezone */
    protected $timezone = null;

    public function __construct(RideNamerListInterface $rideNamerList)
    {
        $this->rideNamerList = $rideNamerList;
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
