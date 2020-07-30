<?php declare(strict_types=1);

namespace App\Criticalmass\RideDuplicates\RideMerger;

use App\Entity\Ride;
use Doctrine\Persistence\ManagerRegistry;

class RideMerger implements RideMergerInterface
{
    /** @var Ride $targetRide */
    protected $targetRide;

    /** @var array $sourceRides */
    protected $sourceRides = [];

    /** @var ManagerRegistry $registry */
    protected $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function setTargetRide(Ride $targetRide): RideMergerInterface
    {
        $this->targetRide = $targetRide;

        return $this;
    }

    public function addSourceRide(Ride $sourceRide): RideMergerInterface
    {
        $this->sourceRides[$sourceRide->getId()] = $sourceRide;

        return $this;
    }

    public function addSourceRides(array $sourceRides): RideMergerInterface
    {
        foreach ($sourceRides as $sourceRide) {
            $this->addSourceRide($sourceRide);
        }

        return $this;
    }

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
