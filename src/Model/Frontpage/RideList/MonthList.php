<?php declare(strict_types=1);

namespace App\Model\Frontpage\RideList;

use App\Entity\Ride;

class MonthList implements \Iterator
{
    protected array $monthList = [];

    public function getMonthList(): array
    {
        return $this->monthList;
    }

    public function addMonth(Month $month): self
    {
        $monthNumber = $month->getDateTime()->format('m');

        $this->monthList[$monthNumber] = $month;

        return $this;
    }

    public function addRide(Ride $ride): self
    {
        $rideMonth = (int) $ride->getDateTime()->format('m');

        if (!array_key_exists($rideMonth, $this->monthList)) {
            $this->monthList[$rideMonth] = new Month();
        }

        $this->monthList[$rideMonth]->addRide($ride);

        return $this;
    }

    public function current(): Month
    {
        return current($this->monthList);
    }

    public function next(): void
    {
        next($this->monthList);
    }

    public function key(): int
    {
        return key($this->monthList);
    }

    public function valid(): bool
    {
        return current($this->monthList) instanceof Month;
    }

    public function rewind(): void
    {
        reset($this->monthList);
    }
}
