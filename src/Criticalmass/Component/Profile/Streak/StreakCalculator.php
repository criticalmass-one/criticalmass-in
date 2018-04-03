<?php

namespace Criticalmass\Component\Profile\Streak;

use Criticalmass\Bundle\AppBundle\Entity\Ride;

class StreakCalculator
{
    /** @var array $list */
    protected $list = [];

    /** @var \DateTime $earliestDateTime */
    protected $earliestDateTime = null;

    /** @var \DateTime $latestDateTime */
    protected $latestDateTime = null;

    public function addRide(Ride $ride): StreakCalculator
    {
        $this->expandList($ride->getDateTime());

        $key = $ride->getDateTime()->format('Y-m');

        $this->list[$key][$ride->getDateTime()->format('Y-m-d')] = $ride;

        return $this;
    }

    protected function expandList(\DateTime $dateTime): StreakCalculator
    {
        if (!$this->earliestDateTime || $this->earliestDateTime > $dateTime) {
            $this->earliestDateTime = $dateTime;
        }

        if (!$this->latestDateTime || $this->latestDateTime < $dateTime) {
            $this->latestDateTime = $dateTime;
        }

        $this->fillGaps();

        return $this;
    }

    protected function fillGaps(): StreakCalculator
    {
        $current = $this->earliestDateTime;
        $month = new \DateInterval('P1M');

        while ($current < $this->latestDateTime) {
            $key = $current->format('Y-m');

            if (!array_key_exists($key, $this->list)) {
                $this->list[$key] = [];
            }

            $current->add($month);
        }

        return $this;
    }

    public function calculateStreakList(): array
    {
    }

    public function calculateCurrentStreak(): Streak
    {

    }

    public function calculateLongestStreak(): Streak
    {
        $counter = 0;
        $startDateTime = null;
        $endDateTime = null;
        $rideList = [];

        foreach ($this->list as $month => $rides) {
            if (!$startDateTime) {
                $startDateTime = new \DateTime(sprintf('%s-01', $month));
            }

            if (count($rides) > 0) {
                ++$counter;

                $rideList = array_merge($rideList, $rides);

                $endDateTime = new \DateTime(sprintf('%s-01', $month));
            } else {
                $startDateTime = null;
                $endDateTime = null;

                $rideList = [];
            }
        }

        $streak = new Streak($startDateTime, $endDateTime, $rideList);

        return $streak;
    }

    public function getList(): array
    {
        return $this->list;
    }
}
