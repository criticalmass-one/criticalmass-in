<?php declare(strict_types=1);

namespace App\Criticalmass\Profile\Streak;

use App\Entity\Ride;

interface StreakCalculatorInterface
{
    public function addRide(Ride $ride): StreakCalculatorInterface;
    public function calculateCurrentStreak(?\DateTime $dateTime = null, bool $includeCurrentMonth = false): ?Streak;
    public function calculateLongestStreak(): ?Streak;
    public function getList(): array;
}
