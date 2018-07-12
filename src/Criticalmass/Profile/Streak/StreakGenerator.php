<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Profile\Streak;

use AppBundle\Entity\Participation;
use AppBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Registry;

class StreakGenerator implements StreakGeneratorInterface
{
    /** @var User $user */
    protected $user;

    /** @var Registry $registry */
    protected $registry;

    /** @var StreakCalculator $calculator */
    protected $calculator;

    public function __construct(Registry $registry)
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

    public function calculateCurrentStreak(\DateTime $currentDateTime = null, bool $includeCurrentMonth = false): ?Streak
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
