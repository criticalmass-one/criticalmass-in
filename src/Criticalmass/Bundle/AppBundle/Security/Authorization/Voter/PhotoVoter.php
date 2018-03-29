<?php

namespace Criticalmass\Bundle\AppBundle\Security\Authorization\Voter;

use Criticalmass\Bundle\AppBundle\Entity\Photo;
use Criticalmass\Bundle\AppBundle\Entity\User;

class PhotoVoter extends AbstractVoter
{
    protected function canView(Photo $photo, User $user): bool
    {
        return true;
    }

    protected function canUpload(Photo $photo, User $user): bool
    {
        return true;
    }

    protected function canEdit(Photo $photo, User $user): bool
    {
        if ($user->hasRole('ROLE_ADMIN')) {
            return true;
        }

        if ($user === $photo->getUser()) {
            return true;
        }

        return false;
    }
}
