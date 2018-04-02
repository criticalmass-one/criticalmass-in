<?php

namespace Criticalmass\Component\Profile\Streak;

use Criticalmass\Bundle\AppBundle\Entity\Participation;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Bundle\AppBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Registry;

class StreakCalculator
{
    /** @var User $user */
    protected $user;

    /** @var Registry $registry */
    protected $registry;

    /** @var array $list */
    protected $list = [];

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function setUser(User $user): StreakCalculator
    {
        $this->user = $user;

        return $this;
    }

    public function calculateStreakList(): array
    {
        $participationList = $this->registry->getRepository(Participation::class)->findByUser($this->user);

        /** @var Participation $participation */
        foreach ($participationList as $participation) {
            $ride = $participation->getRide();

            $this->addRide($ride);
        }

        return $this->list;
    }

    protected function addRide(Ride $ride): StreakCalculator
    {
        $key = $ride->getDateTime()->format('Y-m');

        if (!array_key_exists($key, $this->list)) {
            $this->list[$key] = [];
        }

        $this->list[$key][$ride->getDateTime()->format('Y-m-d')] = $ride;

        return $this;
    }
}
