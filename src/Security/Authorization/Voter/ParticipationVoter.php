<?php declare(strict_types=1);

namespace App\Security\Authorization\Voter;

use App\Entity\Participation;
use App\Entity\User;

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
