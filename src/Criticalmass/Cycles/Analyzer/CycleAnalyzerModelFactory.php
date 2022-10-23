<?php declare(strict_types=1);

namespace App\Criticalmass\Cycles\Analyzer;

use App\Entity\Ride;

class CycleAnalyzerModelFactory implements CycleAnalyzerModelFactoryInterface
{
    protected $rides = [];

    protected $simulatedRides = [];

    protected $resultList = [];

    public function setRides(array $rides): CycleAnalyzerModelFactoryInterface
    {
        $this->rides = $this->organizeList($rides);

        return $this;
    }

    public function setSimulatedRides(array $simulatedRides): CycleAnalyzerModelFactoryInterface
    {
        $this->simulatedRides = $this->organizeList($simulatedRides);

        return $this;
    }

    protected function organizeList(array $rideList): array
    {
        $resultList = [];

        /** @var Ride $ride */
        foreach ($rideList as $ride) {
            $resultList[$ride->getDateTime()->format('Y-m-d')] = $ride;
        }

        ksort($resultList);

        return $resultList;
    }

    public function build(): CycleAnalyzerModelFactoryInterface
    {
        /** @var Ride $simulatedRide */
        /** @var Ride $ride */
        foreach ($this->rides as $ride) {
            $key = $ride->getDateTime()->format('Y-m-d');

            if (array_key_exists($key, $this->simulatedRides)) {
                $simulatedRide = $this->simulatedRides[$key];
                $cycle = $simulatedRide->getCycle();
            } else {
                $simulatedRide = null;
                $cycle = null;
            }

            $model = new CycleAnalyzerModel($ride, $cycle, $simulatedRide);

            $this->resultList[$ride->getDateTime()->format('U')] = $model;
        }

        return $this;
    }

    public function getResultList(): array
    {
        return $this->resultList;
    }
}
