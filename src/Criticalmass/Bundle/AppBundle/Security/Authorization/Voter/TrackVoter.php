<?php

namespace Criticalmass\Bundle\AppBundle\Security\Authorization\Voter;

use Criticalmass\Bundle\AppBundle\Entity\Track;
use Criticalmass\Bundle\AppBundle\Entity\User;

class TrackVoter extends AbstractVoter
{
    protected function canView(Track $track, User $user): bool
    {
        return $this->canEdit($track, $user);
    }

    protected function canDownload(Track $track, User $user): bool
    {
        return $this->canEdit($track, $user);
    }

    protected function canEdit(Track $track, User $user): bool
    {
        if ($user->hasRole('ROLE_ADMIN')) {
            return true;
        }

        if ($user === $track->getUser()) {
            return true;
        }

        return false;
    }
}
