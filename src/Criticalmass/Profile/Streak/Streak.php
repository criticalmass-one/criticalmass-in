<?php declare(strict_types=1);

namespace App\Criticalmass\Profile\Streak;

class Streak
{
    public function __construct(protected \DateTime $startDateTime, protected \DateTime $endDateTime, protected array $rideList)
    {
    }

    public function getStartDateTime(): \DateTime
    {
        return $this->startDateTime;
    }

    public function getEndDateTime(): \DateTime
    {
        return $this->endDateTime;
    }

    public function getRideList(): array
    {
        return $this->rideList;
    }
}
