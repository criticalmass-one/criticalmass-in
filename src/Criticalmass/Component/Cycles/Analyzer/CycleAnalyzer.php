<?php declare(strict_types=1);

namespace Criticalmass\Component\Cycles\Analyzer;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\CityCycle;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Component\RideGenerator\RideCalculator\RideCalculatorInterface;
use Criticalmass\Component\RideGenerator\RideGenerator\RideGenerator;
use Doctrine\Bundle\DoctrineBundle\Registry;

class CycleAnalyzer implements CycleAnalyzerInterface
{
    /** @var City $city */
    protected $city;

    /** @var array $cycleList */
    protected $cycleList = [];

    /** @var array $rideList */
    protected $rideList = [];

    /** @var array $simulatedRideList */
    protected $simulatedRideList = [];

    /** @var Registry $registry */
    protected $registry;

    /** @var RideCalculatorInterface $rideCalculator */
    protected $rideCalculator;

    /** @var \DateTime $startDateTime */
    protected $startDateTime;

    /** @var \DateTime $endDateTime */
    protected $endDateTime;

    /** @var CycleAnalyzerModelFactoryInterface $analyzerModelFactory */
    protected $analyzerModelFactory;

    public function __construct(
        Registry $registry,
        RideCalculatorInterface $rideCalculator,
        CycleAnalyzerModelFactoryInterface $analyzerModelFactory
    ) {
        $this->registry = $registry;

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

        $this->rideCalculator->setCycleList($this->cycleList);

        return $this;
    }

    protected function fetchRides(): CycleAnalyzer
    {
        $this->rideList = $this->registry->getRepository(Ride::class)->findRidesForCity($this->city);

        $this->endDateTime = $this->rideList[0]->getDateTime();
        $this->startDateTime = $this->rideList[count($this->rideList) - 1]->getDateTime();

        return $this;
    }

    protected function simulateRides()
    {
        $month = new \DateInterval('P1M');

        for ($current = $this->startDateTime; $current <= $this->endDateTime; $current->add($month)) {
            $rideList = $this->rideCalculator
                ->reset()
                ->setMonth((int)$current->format('m'))
                ->setYear((int)$current->format('Y'))
                ->setCycleList($this->cycleList)
                ->execute()
                ->getRideList();

            $this->simulatedRideList = array_merge($this->simulatedRideList, $rideList);
        }
    }

    public function getResultList(): array
    {
        return $this->analyzerModelFactory->getResultList();
    }
}
