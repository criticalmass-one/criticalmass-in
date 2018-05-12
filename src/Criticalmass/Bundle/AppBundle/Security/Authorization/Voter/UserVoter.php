<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Security\Authorization\Voter;

use Criticalmass\Bundle\AppBundle\Entity\User;

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
