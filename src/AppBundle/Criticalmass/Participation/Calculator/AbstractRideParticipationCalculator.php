<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Participation\Calculator;

use AppBundle\Entity\Ride;
use Doctrine\Bundle\DoctrineBundle\Registry;

abstract class AbstractRideParticipationCalculator implements RideParticipationCalculatorInterface
{
    /** @var Registry $registry */
    protected $registry;

    /** @var Ride $ride */
    protected $ride;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }
}
