<?php declare(strict_types=1);

namespace App\Model\Frontpage\RideList;

use App\Entity\Ride;

class Day implements \Iterator
{
    /** @var \DateTime $dateTime */
    protected $dateTime = null;

    /** @var array $hourList */
    protected $hourList = [];

    public function __construct(\DateTime $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    public function add(Ride $ride): Day
    {
        $hour = $ride->getDateTime()->format('j');

        if (!array_key_exists($hour, $this->hourList)) {
            $this->hourList[$hour] = new Hour($ride->getDateTime());
        }

        $this->hourList[$hour]->add($ride);

        return $this;
    }

    public function current(): Hour
    {
        return current($this->hourList);
    }

    public function next(): void
    {
        next($this->hourList);
    }

    public function key(): int
    {
        return key($this->hourList);
    }

    public function valid(): bool
    {
        return current($this->hourList) instanceof Hour;
    }

    public function rewind(): void
    {
        reset($this->hourList);
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }
}
