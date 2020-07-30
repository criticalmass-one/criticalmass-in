<?php declare(strict_types=1);

namespace App\Criticalmass\RideDuplicates\RideMerger;

use App\Entity\Ride;

class RideMerger extends AbstractRideMerger
{
    public function merge(): Ride
    {
        /** @var Ride $sourceRide */
        foreach ($this->sourceRides as $sourceRide) {
            $this->targetRide->setViews($this->targetRide->getViews() + $sourceRide->getViews());

            $relationProperties = ['weather', 'estimate', 'track', 'subride', 'post', 'photo', 'socialNetworkProfile', 'viewRelation'];

            foreach ($relationProperties as $relationProperty) {
                $getMethodName = sprintf('get%ss', ucfirst($relationProperty));

                foreach ($sourceRide->$getMethodName() as $relationObject) {
                    $relationObject->setRide($this->targetRide);
                }
            }
        }

        return $this->targetRide;
    }
}
