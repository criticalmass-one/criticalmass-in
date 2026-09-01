<?php declare(strict_types=1);

namespace App\Security\Authorization\Voter;

use App\Entity\Post;
use App\Entity\User;

class PostVoter extends AbstractVoter
{
    protected function canEdit(Post $post, User $user): bool
    {
        if ($user->hasRole('ROLE_ADMIN')) {
            return true;
        }

        return $user === $post->getUser();
    }
}
