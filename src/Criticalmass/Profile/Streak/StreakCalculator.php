<?php declare(strict_types=1);

namespace App\Criticalmass\Profile\Streak;

use App\Entity\Ride;
use Carbon\Carbon;

class StreakCalculator implements StreakCalculatorInterface
{
    /** @var array $list */
    protected $list = [];

    /** @var Carbon $earliestDateTime */
    protected $earliestDateTime = null;

    /** @var Carbon $latestDateTime */
    protected $latestDateTime = null;

    public function addRide(Ride $ride): StreakCalculatorInterface
    {
        $this->expandList($ride->getDateTime());

        $key = $ride->getDateTime()->format('Y-m');

        $this->list[$key][$ride->getDateTime()->format('Y-m-d')] = $ride;

        return $this;
    }

    protected function expandList(Carbon $dateTime): StreakCalculator
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
        $current = Carbon::parse(sprintf('%s-01 00:00:00', $this->earliestDateTime->format('Y-m')));
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
    
    public function calculateCurrentStreak(?Carbon $dateTime = null, bool $includeCurrentMonth = false): ?Streak
    {
        if (!$dateTime) {
            $dateTime = Carbon::now();
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
                    $endDateTime = Carbon::parse(sprintf('%s-01', $month));
                }

                $startDateTime = Carbon::parse(sprintf('%s-01', $month));

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

    protected function checkCurrentStreakMonth(?Carbon $dateTime = null, bool $includeCurrentMonth = false): bool
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
                $startDateTime = Carbon::parse(sprintf('%s-01', $month));
            }

            if (count($rides) > 0) {
                ++$counter;

                $rideList = array_merge($rideList, $rides);

                $endDateTime = Carbon::parse(sprintf('%s-01', $month));

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
