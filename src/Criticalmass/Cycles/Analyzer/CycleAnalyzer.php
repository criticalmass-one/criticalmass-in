<?php declare(strict_types=1);

namespace App\Criticalmass\Cycles\Analyzer;

use App\Entity\CityCycle;

class CycleAnalyzer extends AbstractCycleAnalyzer
{
    public function analyzeCycle(CityCycle $cityCycle): array
    {
        $month = new \DateInterval('P1M');

        $current = $this->startDateTime;

        do {
            $rideList = $this->rideGenerator
                ->setDateTime($current)
                ->($this->cycleList)
                ->execute()
                ->getRideList();

            $this->simulatedRideList = array_merge($this->simulatedRideList, $rideList);

            $current->add($month);
        } while ($current->format('Y-m') <= $this->endDateTime->format('Y-m'));

        return $this;

        return $this->analyzerModelFactory->getResultList();
    }

    protected function simulateRides(): CycleAnalyzer
    {
        $month = new \DateInterval('P1M');

        $current = $this->startDateTime;

        do {
            $rideList = $this->rideCalculator
                ->setMonth((int)$current->format('m'))
                ->setYear((int)$current->format('Y'))
                ->setCycleList($this->cycleList)
                ->execute()
                ->getRideList();

            $this->simulatedRideList = array_merge($this->simulatedRideList, $rideList);

            $current->add($month);
        } while ($current->format('Y-m') <= $this->endDateTime->format('Y-m'));

        return $this;
    }
}
