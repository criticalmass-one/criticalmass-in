<?php declare(strict_types=1);

namespace App\Criticalmass\Cycles\Analyzer;

use App\Entity\CityCycle;
use App\Entity\Ride;
use App\Criticalmass\RideGenerator\RideCalculator\RideCalculatorInterface;
use Doctrine\Persistence\ManagerRegistry;

class CycleAnalyzer extends AbstractCycleAnalyzer
{
    public function analyzeCycle(CityCycle $cityCycle): array
    {
        $month = new \DateInterval('P1M');

        $current = $this->startDateTime;

        do {
            $rideList = $this->rideGenerator
                ->setDateTime($current)
                ->setCycleList($this->cycleList)
                ->execute()
                ->getRideList();

            $this->simulatedRideList = array_merge($this->simulatedRideList, $rideList);

            $current->add($month);
        } while ($current->format('Y-m') <= $this->endDateTime->format('Y-m'));

        return $this->analyzerModelFactory->getResultList();
    }

    protected function simulateRides(): CycleAnalyzer
    {
        $month = new \DateInterval('P1M');

        $current = $this->startDateTime;

        do {
            /** @var CityCycle $cycle */
            foreach ($this->cycleList as $cycle) {
                $ride = $this->rideCalculator
                    ->setMonth((int)$current->format('m'))
                    ->setYear((int)$current->format('Y'))
                    ->setCycle($cycle)
                    ->execute();

                $this->simulatedRideList[] = $ride;
            }
            
            $current->add($month);
        } while ($current->format('Y-m') <= $this->endDateTime->format('Y-m'));

        return $this;
    }
}
