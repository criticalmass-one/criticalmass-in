<?php declare(strict_types=1);

namespace App\Factory;

use App\Entity\Ride;
use App\Model\Frontpage\RideList\Month;
use App\Model\Frontpage\RideList\MonthList;
use Doctrine\Persistence\ManagerRegistry;

class FrontpageRideListFactory
{
    protected ManagerRegistry $doctrine;

    protected MonthList $monthList;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->monthList = new MonthList();
    }

    public function getMonth(int $monthNumber): Month
    {
        return $this->monthList[$monthNumber];
    }

    public function createList(): FrontpageRideListFactory
    {
        $rides = $this->doctrine->getRepository(Ride::class)->findFrontpageRides();

        foreach ($rides as $ride) {
            $this->monthList->addRide($ride);
        }

        return $this;
    }

    public function sort(): self
    {
        /** @var Month $month */
        foreach ($this->monthList as $month) {
            $month->sort();
        }

        return $this;
    }

    public function getMonthList(): MonthList
    {
        return $this->monthList;
    }
}
