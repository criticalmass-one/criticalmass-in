<?php declare(strict_types=1);

namespace App\Security\Authorization\Voter;

use App\Entity\User;

class UserVoter extends AbstractVoter
{
    protected function canDelete(User $subject, User $user): bool
    {
        return $user === $subject || $user->hasRole('ROLE_ADMIN');
    }
}
