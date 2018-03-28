<?php

namespace Criticalmass\Bundle\AppBundle\Security\Authorization\Voter;

use Criticalmass\Bundle\AppBundle\Entity\Photo;
use Criticalmass\Bundle\AppBundle\Entity\User;

class PhotoVoter extends AbstractVoter
{
    protected function canView(Photo $photo, User $user): int
    {
        return self::ACCESS_GRANTED;
    }

    protected function canUpload(Photo $photo, User $user): int
    {
        return self::ACCESS_GRANTED;
    }

    protected function canEdit(Photo $photo, User $user): int
    {
        if ($user->hasRole('ROLE_ADMIN')) {
            return self::ACCESS_GRANTED;
        }

        if ($user === $photo->getUser()) {
            return self::ACCESS_GRANTED;
        }

        return self::ACCESS_ABSTAIN;
    }
}
