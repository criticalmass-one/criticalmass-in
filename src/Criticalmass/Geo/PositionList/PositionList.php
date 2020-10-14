<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\PositionList;

use App\Criticalmass\Geo\EntityInterface\PositionInterface;

class PositionList implements PositionListInterface
{
    /**
     * @var array $list
     */
    protected $list = [];

    public function __construct(array $list = [])
    {
        $this->list = $list;
    }

    public function getStartDateTime(): \DateTime
    {
        return $this->get(0)->getDateTime();
    }

    public function getEndDateTime(): \DateTime
    {
        $count = $this->count();

        return $this->get($count - 1)->getDateTime();
    }

    public function count(): int
    {
        return count($this->list);
    }

    public function getLatitude(int $n): float
    {
        return $this->get($n)->getLatitude();
    }

    public function getLongitude(int $n): float
    {
        return $this->get($n)->getLongitude();
    }

    public function getAltitude(int $n): float
    {
        return $this->get($n)->getAltitude();
    }

    public function getDateTime(int $n): \DateTime
    {
        return $this->get($n)->getDateTime();
    }

    public function get(int $n): PositionInterface
    {
        return $this->list[$n];
    }

    public function set(int $n, PositionInterface $position): PositionListInterface
    {
        $this->list[$n] = $position;

        return $this;
    }

    public function pop(): ?PositionInterface
    {
        return array_pop($this->list);
    }

    public function push(PositionInterface $position): PositionListInterface
    {
        array_push($this->list, $position);

        return $this;
    }

    public function unshift(PositionInterface $position): PositionListInterface
    {
        array_unshift($this->list, $position);

        return $this;
    }

    public function shift(): PositionInterface
    {
        return array_shift($this->list);
    }

    public function add(PositionInterface $position): PositionListInterface
    {
        $this->list[] = $position;

        return $this;
    }

    public function remove(int $n): PositionListInterface
    {
        unset($this->list[$n]);

        return $this;
    }

    public function current()
    {
        return current($this->list);
    }

    public function next(): void
    {
        next($this->list);
    }

    public function key()
    {
        return key($this->list);
    }

    public function valid(): bool
    {
        return true;
    }

    public function rewind(): void
    {
        reset($this->list);
    }

    public function getList(): array
    {
        return $this->list;
    }

    public function setList(array $list): PositionListInterface
    {
        $this->list = $list;

        return $this;
    }
}
