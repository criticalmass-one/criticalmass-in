<?php declare(strict_types=1);

namespace Criticalmass\Component\Profile\Streak;

use Criticalmass\Bundle\AppBundle\Entity\Ride;

interface StreakCalculatorInterface
{
    public function addRide(Ride $ride): StreakCalculatorInterface;
    public function calculateStreakList(): array;
    public function calculateCurrentStreak(\DateTime $dateTime = null, bool $includeCurrentMonth = false): Streak;
    public function calculateLongestStreak(): Streak;
    public function getList(): array;
}
