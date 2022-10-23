<?php declare(strict_types=1);

namespace App\Criticalmass\Profile\ParticipationTable;

use App\Entity\Participation;

class ParticipationDay implements \Countable, \Iterator, \Stringable
{
    /** @var array $participationList */
    protected $participationList = [];

    public function __construct(protected int $year, protected int $month, protected int $day)
    {
    }

    public function addParticipation(Participation $participation): ParticipationDay
    {
        $this->participationList[] = $participation;

        return $this;
    }

    public function count(): int
    {
        return count($this->participationList);
    }

    public function getParticipationList(): array
    {
        return $this->participationList;
    }

    public function getDay(): int
    {
        return $this->day;
    }

    public function __toString(): string
    {
        return (string) $this->day;
    }

    public function current(): ?Participation
    {
        return current($this->participationList);
    }

    public function next(): void
    {
        next($this->participationList);
    }

    public function key(): int
    {
        return key($this->participationList);
    }

    public function rewind(): void
    {
        reset($this->participationList);
    }

    public function valid(): bool
    {
        return true;
    }
}
