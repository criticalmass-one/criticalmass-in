<?php declare(strict_types=1);

namespace App\Criticalmass\Profile\Streak;

use App\Entity\Participation;
use App\Entity\User;
use Carbon\Carbon;
use Doctrine\Persistence\ManagerRegistry;

class StreakGenerator implements StreakGeneratorInterface
{
    /** @var User $user */
    protected $user;

    /** @var ManagerRegistry $registry */
    protected $registry;

    /** @var StreakCalculator $calculator */
    protected $calculator;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;

        $this->calculator = new StreakCalculator();
    }

    public function setUser(User $user): StreakGeneratorInterface
    {
        $this->user = $user;

        return $this;
    }

    protected function loadParticipations(): StreakGenerator
    {
        $participationList = $this->registry->getRepository(Participation::class)->findByUser($this->user);

        /** @var Participation $participation */
        foreach ($participationList as $participation) {
            $ride = $participation->getRide();

            $this->calculator->addRide($ride);
        }

        return $this;
    }

    public function calculateCurrentStreak(?Carbon $currentDateTime = null, bool $includeCurrentMonth = false): ?Streak
    {
        $this->loadParticipations();

        return $this->calculator->calculateCurrentStreak($currentDateTime, $includeCurrentMonth);
    }

    public function calculateLongestStreak(): ?Streak
    {
        $this->loadParticipations();

        return $this->calculator->calculateLongestStreak();
    }
}
