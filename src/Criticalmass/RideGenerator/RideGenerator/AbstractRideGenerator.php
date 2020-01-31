<?php declare(strict_types=1);

namespace App\Criticalmass\RideGenerator\RideGenerator;

use App\Criticalmass\RideGenerator\RideCalculator\RideCalculator;
use App\Criticalmass\RideGenerator\RideCalculator\RideCalculatorInterface;
use App\Criticalmass\RideNamer\RideNamerListInterface;
use App\Criticalmass\Util\DateTimeUtil;
use App\Entity\CityCycle;
use App\Entity\Ride;
use Symfony\Bridge\Doctrine\RegistryInterface;

abstract class AbstractRideGenerator implements RideGeneratorInterface
{
    /** @var array $dateTimeList */
    protected $dateTimeList = [];

    /** @var array $rideList */
    protected $rideList = [];

    /** @var RegistryInterface $doctrine */
    protected $doctrine;

    /** @var RideNamerListInterface $rideNamerList */
    protected $rideNamerList;

    public function __construct(RegistryInterface $doctrine, RideNamerListInterface $rideNamerList)
    {
        $this->doctrine = $doctrine;
        $this->rideNamerList = $rideNamerList;
    }

    public function setDateTime(\DateTime $dateTime): RideGeneratorInterface
    {
        $this->dateTimeList = [$dateTime];

        return $this;
    }

    public function addDateTime(\DateTime $dateTime): RideGeneratorInterface
    {
        $this->dateTimeList[] = $dateTime;

        return $this;
    }

    public function setDateTimeList(array $dateTimeList): RideGeneratorInterface
    {
        $this->dateTimeList = $dateTimeList;

        return $this;
    }

    public function getRideList(): array
    {
        return $this->rideList;
    }

    protected function hasRideAlreadyBeenCreated(CityCycle $cityCycle, \DateTime $startDateTime): bool
    {
        $endDateTime = DateTimeUtil::getMonthEndDateTime($startDateTime);

        $existingRides = $this->doctrine->getRepository(Ride::class)->findRidesByCycleInInterval($cityCycle, $startDateTime, $endDateTime);

        return count($existingRides) > 0;
    }

    protected function getRideCalculatorForCycle(CityCycle $cityCycle): RideCalculatorInterface
    {
        if (($rideCalculatorFqcn = $cityCycle->getRideCalculatorFqcn()) && class_exists($rideCalculatorFqcn)) {
            return new $rideCalculatorFqcn($this->rideNamerList);
        }

        return new RideCalculator($this->rideNamerList);
    }

    public abstract function execute(): RideGeneratorInterface;
}
