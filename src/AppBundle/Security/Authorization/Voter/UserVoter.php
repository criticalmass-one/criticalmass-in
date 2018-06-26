<?php declare(strict_types=1);

namespace AppBundle\Security\Authorization\Voter;

use AppBundle\Entity\User;

class UserVoter extends AbstractVoter
{
    protected function canCancel(User $user, User $actor): bool
    {
        if ($actor->hasRole('ROLE_ADMIN')) {
            return true;
        }

        if ($user === $actor) {
            return true;
        }

        return false;
    }
}
