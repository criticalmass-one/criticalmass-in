<?php declare(strict_types=1);

namespace App\Criticalmass\Profile\Streak;

use App\Entity\Ride;

class StreakCalculator implements StreakCalculatorInterface
{
    /** @var array $list */
    protected $list = [];

    /** @var \DateTime $earliestDateTime */
    protected $earliestDateTime = null;

    /** @var \DateTime $latestDateTime */
    protected $latestDateTime = null;

    public function addRide(Ride $ride): StreakCalculatorInterface
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
        $current = new \DateTime(sprintf('%s-01 00:00:00', $this->earliestDateTime->format('Y-m')));
        $month = new \DateInterval('P1M');

        while ($current->format('Y-m') < $this->latestDateTime->format('Y-m')) {
            $key = $current->format('Y-m');

            if (!array_key_exists($key, $this->list)) {
                $this->list[$key] = [];
            }

            $current->add($month);
        }

        return $this;
    }
    
    public function calculateCurrentStreak(\DateTime $dateTime = null, bool $includeCurrentMonth = false): ?Streak
    {
        if (!$dateTime) {
            $dateTime = new \DateTime();
        }

        krsort($this->list);

        if (!$this->checkCurrentStreakMonth($dateTime, $includeCurrentMonth)) {
            return new Streak($dateTime, $dateTime, []);
        }

        $longestStreakCounter = null;
        $longestStreak = null;

        $startDateTime = null;
        $endDateTime = null;
        $rideList = [];

        foreach ($this->list as $month => $rides) {
            if (count($rides) > 0) {
                if (!$endDateTime) {
                    $endDateTime = new \DateTime(sprintf('%s-01', $month));
                }

                $startDateTime = new \DateTime(sprintf('%s-01', $month));

                $rideList = array_merge($rideList, $rides);
            } else {
                break;
            }
        }

        if ($startDateTime && $endDateTime && count($rideList) > 0) {
            return new Streak($startDateTime, $endDateTime, $rideList);
        }

        return null;
    }

    protected function checkCurrentStreakMonth(\DateTime $dateTime = null, bool $includeCurrentMonth = false): bool
    {
        $monthKeys = array_keys($this->list);

        $lastMonthString = array_shift($monthKeys);

        if (!$includeCurrentMonth) {
            $dateTime->sub(new \DateInterval('P1M'));
        }

        return $lastMonthString === $dateTime->format('Y-m');
    }

    public function calculateLongestStreak(): ?Streak
    {
        $counter = 0;
        $longestStreakCounter = null;
        $longestStreak = null;

        $startDateTime = null;
        $endDateTime = null;
        $rideList = [];

        ksort($this->list);

        foreach ($this->list as $month => $rides) {
            if (!$startDateTime) {
                $startDateTime = new \DateTime(sprintf('%s-01', $month));
            }

            if (count($rides) > 0) {
                ++$counter;

                $rideList = array_merge($rideList, $rides);

                $endDateTime = new \DateTime(sprintf('%s-01', $month));

                if ($longestStreakCounter === null || $counter >= $longestStreakCounter) {
                    $longestStreakCounter = $counter;
                    $longestStreak = new Streak($startDateTime, $endDateTime, $rideList);
                }
            } else {
                $counter = 0;

                $startDateTime = null;
                $endDateTime = null;

                $rideList = [];
            }
        }

        return $longestStreak;
    }

    public function getList(): array
    {
        return $this->list;
    }
}
