<?php declare(strict_types=1);

namespace App\Criticalmass\RideGenerator\RideGenerator;

use App\Criticalmass\RideNamer\RideNamerListInterface;
use App\Entity\City;
use Symfony\Bridge\Doctrine\RegistryInterface;

abstract class AbstractRideGenerator implements RideGeneratorInterface
{
    /** @var array $dateTimeList */
    protected $dateTimeList = [];

    /** @var array $cityList */
    protected $cityList;

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

    public abstract function execute(): RideGeneratorInterface;
}
