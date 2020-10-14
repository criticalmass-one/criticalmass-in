<?php declare(strict_types=1);

namespace App\Model\Frontpage\RideList;

use App\Entity\Ride;

class Month implements \Iterator
{
    /** @var array $dayList */
    protected $dayList = [];

    /** @var \DateTime $dateTime */
    protected $dateTime = null;

    public function __construct()
    {
        $this->dateTime = new \DateTime();
    }

    public function add(Ride $ride): Month
    {
        $day = $ride->getDateTime()->format('j');

        if (!array_key_exists($day, $this->dayList)) {
            $this->dayList[$day] = new Day($ride->getDateTime());
        }

        $this->dayList[$day]->add($ride);

        return $this;
    }

    public function current(): Day
    {
        return current($this->dayList);
    }

    public function next(): void
    {
        next($this->dayList);
    }

    public function key(): int
    {
        return key($this->dayList);
    }

    public function valid(): bool
    {
        return current($this->dayList) instanceof Day;
    }

    public function rewind(): void
    {
        reset($this->dayList);
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    public function sort(): Month
    {
        /** @var Day $day */
        foreach ($this->dayList as $day) {
            $day->sort();
        }
        return $this;
    }
}
