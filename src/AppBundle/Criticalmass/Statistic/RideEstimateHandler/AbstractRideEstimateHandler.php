<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Statistic\RideEstimateHandler;

use AppBundle\Criticalmass\Statistic\RideEstimateCalculator\RideEstimateCalculatorInterface;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEstimate;
use AppBundle\Repository\RideEstimateRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\RegistryInterface;

abstract class AbstractRideEstimateHandler implements RideEstimateHandlerInterface
{
    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var RideEstimateCalculatorInterface $calculator */
    protected $calculator;

    /** @var Ride $ride */
    protected $ride;

    public function __construct(RegistryInterface $registry, RideEstimateCalculatorInterface $calculator)
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
