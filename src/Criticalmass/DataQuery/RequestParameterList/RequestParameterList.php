<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\RequestParameterList;

class RequestParameterList implements \IteratorAggregate
{
    protected array $list = [];

    public function add(string $key, string $value): self
    {
        $this->list[$key] = $value;

        return $this;
    }

    public function get(string $key): ?string
    {
        return $this->list[$key] ?? null;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->list);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->list);
    }

    public function toArray(): array
    {
        return $this->list;
    }
}
