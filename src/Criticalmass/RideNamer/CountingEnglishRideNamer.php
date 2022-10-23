<?php declare(strict_types=1);

namespace App\Criticalmass\RideNamer;

use App\Entity\Ride;
use Doctrine\Persistence\ManagerRegistry;

class CountingEnglishRideNamer implements RideNamerInterface
{
    public function __construct(protected ManagerRegistry $registry)
    {
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
