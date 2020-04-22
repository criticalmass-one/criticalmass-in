<?php declare(strict_types=1);

namespace App\Criticalmass\RideDuplicates\DuplicateFinder;

use App\Entity\City;
use App\Entity\Ride;
use Doctrine\Persistence\ManagerRegistry;

class DuplicateFinder implements DuplicateFinderInterface
{
    /** @var City $city */
    protected $city;

    /** @var ManagerRegistry $registry */
    protected $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function setCity(City $city): DuplicateFinderInterface
    {
        $this->city = $city;

        return $this;
    }

    public function findDuplicates(): array
    {
        $rideList = $this->findRides();

        $filteredRideList = [];
        $duplicateRideList = [];

        foreach ($rideList as $key => $ride) {
            $cityId = $ride->getCity()->getId();

            if (!array_key_exists($cityId, $filteredRideList)) {
                $filteredRideList[$cityId] = [];
            }

            $filteredRideList[$cityId][] = $ride;
        }

        foreach ($filteredRideList as $cityId => $cityRideList) {
            while (0 !== count($cityRideList)) {
                $ride = array_pop($cityRideList);

                foreach ($cityRideList as $otherKey => $otherRide) {
                    if ($ride->getDateTime()->format('Y-m-d') === $otherRide->getDateTime()->format('Y-m-d')) {
                        $duplicateKey = sprintf('%d-%s', $cityId, $ride->getDateTime()->format('Y-m-d'));

                        if (!array_key_exists($duplicateKey, $duplicateRideList)) {
                            $duplicateRideList[$duplicateKey][$ride->getId()] = $ride;
                        }

                        $duplicateRideList[$duplicateKey][$otherRide->getId()] = $otherRide;
                    }
                }
            }
        }

        return $duplicateRideList;
    }

    protected function findRides(): array
    {
        if ($this->city) {
            return $this->registry->getRepository(Ride::class)->findRidesForCity($this->city);
        } else {
            return $this->registry->getRepository(Ride::class)->findAll();
        }
    }
}
