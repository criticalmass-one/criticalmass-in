<?php declare(strict_types=1);

namespace Criticalmass\Component\Profile\Streak;

use Criticalmass\Bundle\AppBundle\Entity\Participation;
use Criticalmass\Bundle\AppBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Registry;

class StreakGenerator
{
    /** @var User $user */
    protected $user;

    /** @var Registry $registry */
    protected $registry;

    /** @var StreakCalculator $calculator */
    protected $calculator;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;

        $this->calculator = new StreakCalculator();
    }

    public function setUser(User $user): StreakGenerator
    {
        $this->user = $user;

        return $this;
    }

    public function calculate(): StreakGenerator
    {
        $participationList = $this->registry->getRepository(Participation::class)->findByUser($this->user);

        /** @var Participation $participation */
        foreach ($participationList as $participation) {
            $ride = $participation->getRide();

            $this->calculator->addRide($ride);
        }

        return $this;
    }
}
