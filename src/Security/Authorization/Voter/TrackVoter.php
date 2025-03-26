<?php declare(strict_types=1);

namespace App\Security\Authorization\Voter;

use App\Entity\Track;
use App\Entity\User;
use Flagception\Manager\FeatureManagerInterface;

class TrackVoter extends AbstractVoter
{
    public function __construct(private readonly FeatureManagerInterface $featureManager)
    {


    }

    protected function canPublicView(Track $track, User $user)
    {
        if ($this->featureManager->isActive('strava_track_view_public')) {
            return true;
        }

        return $this->canView($track, $user);
    }

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
