<?php declare(strict_types=1);

namespace App\Criticalmass\RideDuplicates\DuplicateFinder;

class DuplicateFinder extends AbstractDuplicateFinder
{
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
}
