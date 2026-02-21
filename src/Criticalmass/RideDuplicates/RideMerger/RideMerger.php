<?php declare(strict_types=1);

namespace App\Criticalmass\RideDuplicates\RideMerger;

use App\Entity\Ride;

class RideMerger extends AbstractRideMerger
{
    public function merge(): Ride
    {
        /** @var Ride $sourceRide */
        foreach ($this->sourceRides as $sourceRide) {
            $relationProperties = ['weather', 'estimate', 'track', 'subride', 'post', 'photo', 'socialNetworkProfile'];

            foreach ($relationProperties as $relationProperty) {
                $getMethodName = sprintf('get%ss', ucfirst($relationProperty));
                $addMethodName = sprintf('add%s', ucfirst($relationProperty));
                $removeMethodName = sprintf('remove%s', ucfirst($relationProperty));

                foreach ($sourceRide->$getMethodName() as $relationObject) {
                    $sourceRide->$removeMethodName($relationObject);
                    $this->targetRide->$addMethodName($relationObject);

                    $relationObject->setRide($this->targetRide);
                }
            }
        }

        return $this->targetRide;
    }
}
