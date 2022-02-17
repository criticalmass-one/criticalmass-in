<?php declare(strict_types=1);

namespace App\Factory;

use App\Entity\Ride;
use App\Model\Frontpage\RideList\Month;
use Doctrine\Persistence\ManagerRegistry;

class FrontpageRideListFactory
{
    protected ManagerRegistry $doctrine;

    protected MonthList $monthList;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getMonth(): Month
    {
        if (!$this->monthModel) {
            $this->createList();
        }

        return $this->monthModel;
    }

    public function sort(): Month
    {
        if (!$this->monthModel) {
            $this->createList();
        }

        return $this->monthModel->sort();
    }

    protected function createList(): FrontpageRideListFactory
    {
        $rides = $this->doctrine->getRepository(Ride::class)->findFrontpageRides();

        $this->monthModel = new Month();

        foreach ($rides as $ride) {
            $this->monthModel->add($ride);
        }

        return $this;
    }
}
