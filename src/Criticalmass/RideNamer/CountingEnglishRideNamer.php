<?php declare(strict_types=1);

namespace App\Criticalmass\RideNamer;

use App\Entity\Ride;
use Doctrine\Persistence\ManagerRegistry;

class CountingEnglishRideNamer implements RideNamerInterface
{
    /** @var ManagerRegistry $registry */
    protected $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    protected function countRides(Ride $ride): int
    {
        return $this->registry->getRepository(Ride::class)->countRidesByCity($ride->getCity());
    }

    public function generateTitle(Ride $ride): string
    {
        $cityTitle = $ride->getCity()->getTitle();

        return sprintf('%dth %s', ($this->countRides($ride) + 1), $cityTitle);
    }
}
