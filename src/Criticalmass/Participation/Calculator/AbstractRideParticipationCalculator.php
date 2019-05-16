<?php declare(strict_types=1);

namespace App\Criticalmass\Participation\Calculator;

use App\Entity\Ride;
use Symfony\Bridge\Doctrine\RegistryInterface;

abstract class AbstractRideParticipationCalculator implements RideParticipationCalculatorInterface
{
    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var Ride $ride */
    protected $ride;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }
}
