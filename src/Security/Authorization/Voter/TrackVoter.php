<?php declare(strict_types=1);

namespace App\Security\Authorization\Voter;

use App\Entity\Track;
use App\Entity\User;

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

    protected function canApprove(Track $track, User $user): bool
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
