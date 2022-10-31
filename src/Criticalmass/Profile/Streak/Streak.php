<?php declare(strict_types=1);

namespace App\Criticalmass\Profile\Streak;

class Streak
{
    protected $startDateTime;
    protected $endDateTime;
    protected $rideList = [];

    public function __construct(\DateTime $startDateTime, \DateTime $endDateTime, array $rideList)
    {
        $this->startDateTime = $startDateTime;
        $this->endDateTime = $endDateTime;

        $this->rideList = $rideList;
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
