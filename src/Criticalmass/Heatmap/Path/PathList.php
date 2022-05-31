<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Path;

class PathList implements \Countable, \Iterator
{
    /** @var array $list */
    protected $list = [];

    protected $index = 0;

    public function add(Path $path): PathList
    {
        $this->list[] = $path;

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

    public function key(): int
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

    public function count(): int
    {
        return count($this->list);
    }
}