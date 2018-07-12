<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Profile\Streak;

use AppBundle\Entity\User;

interface StreakGeneratorInterface
{
    public function setUser(User $user): StreakGeneratorInterface;
    public function calculateCurrentStreak(\DateTime $currentDateTime = null, bool $includeCurrentMonth = false): ?Streak;
    public function calculateLongestStreak(): ?Streak;
}
