<?php declare(strict_types=1);

namespace App\Criticalmass\Cycles\Analyzer;

use App\Entity\City;
use App\Entity\CityCycle;
use App\Entity\Ride;
use App\Criticalmass\RideGenerator\RideCalculator\RideCalculatorInterface;
use Doctrine\Persistence\ManagerRegistry;

class CycleAnalyzer extends AbstractCycleAnalyzer
{
    protected function simulateRides(): CycleAnalyzer
    {
        $month = new \DateInterval('P1M');

        $current = $this->startDateTime;

        do {
            $rideList = $this->rideCalculator
                ->reset()
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
