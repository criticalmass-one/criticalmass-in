<?php declare(strict_types=1);

namespace App\Criticalmass\Statistic\RideEstimateHandler;

use App\Criticalmass\Statistic\RideEstimateCalculator\RideEstimateCalculatorInterface;
use App\Entity\Ride;
use App\Entity\RideEstimate;
use App\Repository\RideEstimateRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

abstract class AbstractRideEstimateHandler implements RideEstimateHandlerInterface
{
    protected ManagerRegistry $registry;
    protected RideEstimateCalculatorInterface $calculator;
    protected Ride $ride;

    public function __construct(ManagerRegistry $registry, RideEstimateCalculatorInterface $calculator)
    {
        $this->registry = $registry;
        $this->calculator = $calculator;
    }

    public function setRide(Ride $ride): RideEstimateHandlerInterface
    {
        $this->ride = $ride;

        return $this;
    }

    protected function getRideEstimateRepository(): RideEstimateRepository
    {
        return $this->registry->getRepository(RideEstimate::class);
    }

    protected function getEntityManager(): ObjectManager
    {
        return $this->registry->getManager();
    }
}
