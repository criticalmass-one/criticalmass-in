<?php declare(strict_types=1);

namespace App\Security\Authorization\Voter;

use App\Entity\RideEstimate;
use App\Entity\User;

class RideEstimateVoter extends AbstractVoter
{
    protected function canEdit(RideEstimate $rideEstimate, User $user): bool
    {
        if ($user->hasRole('ROLE_ADMIN')) {
            return true;
        }

        if ($user === $rideEstimate->getUser()) {
            return true;
        }

        return false;
    }

    protected function canDelete(RideEstimate $rideEstimate, User $user): bool
    {
        return $this->canEdit($rideEstimate, $user);
    }
}
