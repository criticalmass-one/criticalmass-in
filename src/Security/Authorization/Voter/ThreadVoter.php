<?php declare(strict_types=1);

namespace App\Security\Authorization\Voter;

use App\Entity\Thread;
use App\Entity\User;

class ThreadVoter extends AbstractVoter
{
    /**
     * Wer das Thema eröffnet hat, darf seinen Titel ändern — erkennbar am Autor des ersten Beitrags.
     */
    protected function canEdit(Thread $thread, User $user): bool
    {
        if ($user->hasRole('ROLE_ADMIN')) {
            return true;
        }

        $firstPost = $thread->getFirstPost();

        return null !== $firstPost && $user === $firstPost->getUser();
    }
}
