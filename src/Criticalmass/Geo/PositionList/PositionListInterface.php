<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\PositionList;

use App\Criticalmass\Geo\EntityInterface\PositionInterface;

interface PositionListInterface extends \Countable, \Iterator
{
    public function getStartDateTime(): \DateTime;

    public function getEndDateTime(): \DateTime;

    public function count(): int;

    public function getLatitude(int $n): float;

    public function getLongitude(int $n): float;

    public function getAltitude(int $n): float;

    public function getDateTime(int $n): \DateTime;

    public function get(int $n): ?PositionInterface;

    public function set(int $n, PositionInterface $position): PositionListInterface;

    public function pop(): ?PositionInterface;

    public function push(PositionInterface $position): PositionListInterface;

    public function shift(): ?PositionInterface;

    public function unshift(PositionInterface $position): PositionListInterface;

    public function add(PositionInterface $position): PositionListInterface;

    public function remove(int $n): PositionListInterface;

    public function getList(): array;

    public function setList(array $list): PositionListInterface;
}
