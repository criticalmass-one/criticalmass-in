<?php declare(strict_types=1);

namespace App\Criticalmass\Profile\Streak;

use App\Entity\User;
use Carbon\Carbon;

interface StreakGeneratorInterface
{
    public function setUser(User $user): StreakGeneratorInterface;
    public function calculateCurrentStreak(?Carbon $currentDateTime = null, bool $includeCurrentMonth = false): ?Streak;
    public function calculateLongestStreak(): ?Streak;
}
