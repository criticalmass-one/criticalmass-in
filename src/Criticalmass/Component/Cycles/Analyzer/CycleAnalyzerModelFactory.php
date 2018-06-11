<?php declare(strict_types=1);

namespace Criticalmass\Component\Cycles\Analyzer;

use Criticalmass\Bundle\AppBundle\Entity\Ride;

class CycleAnalyzerModelFactory implements CycleAnalyzerModelFactoryInterface
{
    protected $rides = [];

    protected $simulatedRides = [];

    protected $resultList = [];

    public function setRides(array $rides): CycleAnalyzerModelFactoryInterface
    {
        $this->rides = $rides;

        return $this;
    }

    public function setSimulatedRides(array $simulatedRides): CycleAnalyzerModelFactoryInterface
    {
        $this->simulatedRides = $simulatedRides;

        return $this;
    }

    public function build(): CycleAnalyzerModelFactoryInterface
    {
        /** @var Ride $simulatedRide */
        foreach ($this->simulatedRides as $simulatedRide) {
            $model = new CycleAnalyzerModel($simulatedRide->getCycle(), $simulatedRide, $simulatedRide);

            $this->resultList[$simulatedRide->getDateTime()->format('U')] = $model;
        }

        return $this;
    }

    public function getResultList(): array
    {
        return $this->resultList;
    }
}
