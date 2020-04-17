<?php declare(strict_types=1);

namespace App\Criticalmass\Statistic\RideEstimateHandler;

use App\Criticalmass\Statistic\RideEstimateCalculator\RideEstimateCalculatorInterface;
use App\Entity\Ride;
use App\Entity\RideEstimate;
use App\Repository\RideEstimateRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractRideEstimateHandler implements RideEstimateHandlerInterface
{
    /** @var ManagerRegistry $registry */
    protected $registry;

    /** @var RideEstimateCalculatorInterface $calculator */
    protected $calculator;

    /** @var Ride $ride */
    protected $ride;

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
