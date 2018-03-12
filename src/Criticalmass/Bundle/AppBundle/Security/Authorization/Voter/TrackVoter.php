<?php

namespace Criticalmass\Bundle\AppBundle\Security\Authorization\Voter;

use Criticalmass\Bundle\AppBundle\Entity\Track;
use Criticalmass\Bundle\AppBundle\Entity\User;

class TrackVoter extends AbstractVoter
{
    protected function canView(Track $track, User $user): int
    {
        return $this->canEdit($track, $user);
    }

    protected function canDownload(Track $track, User $user): int
    {
        return $this->canEdit($track, $user);
    }

    protected function canEdit(Track $track, User $user): int
    {
        if ($user->hasRole('ROLE_ADMIN')) {
            return self::ACCESS_GRANTED;
        }

        if ($user === $track->getUser()) {
            return self::ACCESS_GRANTED;
        }

        return self::ACCESS_ABSTAIN;
    }
}
