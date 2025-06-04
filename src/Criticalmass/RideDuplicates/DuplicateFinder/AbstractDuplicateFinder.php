<?php declare(strict_types=1);

namespace App\Criticalmass\RideDuplicates\DuplicateFinder;

use App\Entity\City;
use App\Entity\Ride;
use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractDuplicateFinder implements DuplicateFinderInterface
{
    protected ?City $city = null;

    protected ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function setCity(City $city): DuplicateFinderInterface
    {
        $this->city = $city;

        return $this;
    }

    protected function findRides(): array
    {
        if ($this->city) {
            return $this->registry->getRepository(Ride::class)->findRidesForCity($this->city);
        }

        return $this->registry->getRepository(Ride::class)->findAll();
    }
}
