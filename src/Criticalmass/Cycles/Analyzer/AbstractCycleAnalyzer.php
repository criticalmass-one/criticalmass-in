<?php declare(strict_types=1);

namespace App\Criticalmass\Cycles\Analyzer;

use App\Criticalmass\RideGenerator\RideCalculator\RideCalculatorInterface;
use App\Criticalmass\RideGenerator\RideGenerator\CityRideGeneratorInterface;
use App\Criticalmass\RideGenerator\RideGenerator\RideGeneratorInterface;
use App\Entity\City;
use App\Entity\CityCycle;
use App\Entity\Ride;
use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractCycleAnalyzer implements CycleAnalyzerInterface
{
    /** @var City $city */
    protected $city;

    /** @var array $cycleList */
    protected $cycleList = [];

    /** @var array $rideList */
    protected $rideList = [];

    /** @var array $simulatedRideList */
    protected $simulatedRideList = [];

    /** @var ManagerRegistry $registry */
    protected $registry;

    /** @var RideGeneratorInterface $rideGenerator */
    protected $rideGenerator;

    /** @var RideCalculatorInterface $rideCalculator */
    protected $rideCalculator;

    /** @var \DateTime $startDateTime */
    protected $startDateTime = null;

    /** @var \DateTime $endDateTime */
    protected $endDateTime = null;

    /** @var CycleAnalyzerModelFactoryInterface $analyzerModelFactory */
    protected $analyzerModelFactory;

    public function __construct(
        ManagerRegistry $registry,
        CityRideGeneratorInterface $rideGenerator,
        RideCalculatorInterface $rideCalculator,
        CycleAnalyzerModelFactoryInterface $analyzerModelFactory
    )
    {
        $this->registry = $registry;

        $this->rideGenerator = $rideGenerator;

        $this->rideCalculator = $rideCalculator;

        $this->analyzerModelFactory = $analyzerModelFactory;
    }

    public function setCity(City $city): CycleAnalyzerInterface
    {
        $this->city = $city;
        $this->fetchCycles();

        return $this;
    }

    public function analyze(): CycleAnalyzerInterface
    {
        $this->fetchRides();

        $this->simulateRides();

        $this->analyzerModelFactory
            ->setRides($this->rideList)
            ->setSimulatedRides($this->simulatedRideList)
            ->build();

        return $this;
    }

    protected function fetchCycles(): CycleAnalyzer
    {
        $this->cycleList = $this->registry->getRepository(CityCycle::class)->findByCity($this->city);

        //$this->rideCalculator->setCycleList($this->cycleList);

        return $this;
    }

    protected function fetchRides(): CycleAnalyzer
    {
        $this->rideList = $this->registry->getRepository(Ride::class)->findRides($this->startDateTime, $this->endDateTime, $this->city);

        if (count($this->rideList) === 0) {
            return $this;
        }

        if (!$this->endDateTime) {
            $this->endDateTime = $this->rideList[0]->getDateTime();
        }

        if (!$this->startDateTime) {
            $this->startDateTime = $this->rideList[count($this->rideList) - 1]->getDateTime();
        }

        return $this;
    }

    public function getResultList(): array
    {
        return $this->analyzerModelFactory->getResultList();
    }

    public function setStartDateTime(\DateTime $dateTime): CycleAnalyzerInterface
    {
        $this->startDateTime = $dateTime;

        return $this;
    }

    public function setEndDateTime(\DateTime $dateTime): CycleAnalyzerInterface
    {
        $this->endDateTime = $dateTime;

        return $this;
    }

    protected abstract function simulateRides(): CycleAnalyzer;
}
