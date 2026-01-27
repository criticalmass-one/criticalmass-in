<?php declare(strict_types=1);

namespace App\Criticalmass\Profile\Streak;

use Carbon\Carbon;

class Streak
{
    protected $startDateTime;
    protected $endDateTime;
    protected $rideList = [];

    public function __construct(Carbon $startDateTime, Carbon $endDateTime, array $rideList)
    {
        $this->startDateTime = $startDateTime;
        $this->endDateTime = $endDateTime;

        $this->rideList = $rideList;
    }

    public function getStartDateTime(): Carbon
    {
        return $this->startDateTime;
    }

    public function getEndDateTime(): Carbon
    {
        return $this->endDateTime;
    }

    public function getRideList(): array
    {
        return $this->rideList;
    }
}
