<?php declare(strict_types=1);

namespace App\Criticalmass\Participation\Calculator;

use App\Entity\Ride;
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
