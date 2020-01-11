<?php declare(strict_types=1);

namespace App\Criticalmass\RideGenerator\RideCalculator;

use App\Criticalmass\RideNamer\RideNamerListInterface;
use App\Entity\CityCycle;

abstract class AbstractRideCalculator implements RideCalculatorInterface
{
    /** @var array $dateTimeList */
    protected $dateTimeList = [];

    /** @var array $cycle */
    protected $cycleList = [];

    /** @var array $rideList */
    protected $rideList = [];

    /** @var RideNamerListInterface $rideNamerList */
    protected $rideNamerList = [];

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
    
    public function addDateTime(\DateTime $dateTime): RideCalculatorInterface
    {
        $this->dateTimeList[] = $dateTime;

        return $this;
    }

    public function setDateTime(\DateTime $dateTime): RideCalculatorInterface
    {
        $this->dateTimeList = [$dateTime];

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
