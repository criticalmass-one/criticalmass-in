<?php declare(strict_types=1);

namespace App\Criticalmass\Profile\Streak;

use App\Entity\Ride;
use Carbon\Carbon;

interface StreakCalculatorInterface
{
    public function addRide(Ride $ride): StreakCalculatorInterface;
    public function calculateCurrentStreak(?Carbon $dateTime = null, bool $includeCurrentMonth = false): ?Streak;
    public function calculateLongestStreak(): ?Streak;
    public function getList(): array;
}
