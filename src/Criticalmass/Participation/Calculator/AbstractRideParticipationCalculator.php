<?php declare(strict_types=1);

namespace App\Criticalmass\Participation\Calculator;

use App\Entity\Ride;
use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractRideParticipationCalculator implements RideParticipationCalculatorInterface
{
    /** @var Ride $ride */
    protected $ride;

    public function __construct(protected ManagerRegistry $registry)
    {
    }
}
