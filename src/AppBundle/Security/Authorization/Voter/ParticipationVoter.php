<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Security\Authorization\Voter;

use Criticalmass\Bundle\AppBundle\Entity\Participation;
use Criticalmass\Bundle\AppBundle\Entity\User;

class ParticipationVoter extends AbstractVoter
{
    protected function canCancel(Participation $participation, User $user): bool
    {
        if ($user->hasRole('ROLE_ADMIN')) {
            return true;
        }

        if ($user === $participation->getUser()) {
            return true;
        }

        return false;
    }

    protected function canDelete(Participation $participation, User $user): bool
    {
        if ($user->hasRole('ROLE_ADMIN')) {
            return true;
        }

        if ($user === $participation->getUser()) {
            return true;
        }

        return false;
    }
}
