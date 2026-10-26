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

    /**
     * Der erste Beitrag traegt das Thema — er laesst sich nicht einzeln zurueckziehen,
     * sonst bliebe ein Thema ohne Anfang stehen. Dafuer gibt es das Loeschen des Themas.
     */
    protected function canDelete(Post $post, User $user): bool
    {
        $thread = $post->getThread();

        if (null !== $thread && $thread->getFirstPost() === $post) {
            return false;
        }

        return $this->canEdit($post, $user);
    }
}
