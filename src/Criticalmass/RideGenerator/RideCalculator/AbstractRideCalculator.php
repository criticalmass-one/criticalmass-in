<?php declare(strict_types=1);

namespace App\Criticalmass\RideGenerator\RideCalculator;

use App\Criticalmass\RideNamer\RideNamerListInterface;
use App\Entity\CityCycle;
use App\Entity\Ride;

abstract class AbstractRideCalculator implements RideCalculatorInterface
{
    /** @var int $month */
    protected $month;

    /** @var int $year */
    protected $year;

    /** @var CityCycle $cycle */
    protected $cycle;

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

    public function setCycle(CityCycle $cityCycle): RideCalculatorInterface
    {
        $this->cycle = $cityCycle;

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

    public abstract function execute(): ?Ride;
}
