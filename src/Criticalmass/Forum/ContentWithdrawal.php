<?php declare(strict_types=1);

namespace App\Criticalmass\Forum;

use App\Entity\Post;
use App\Entity\Thread;
use App\EntityInterface\BoardInterface;
use App\Repository\PostRepository;

/**
 * Nimmt Beitraege und Themen aus der Anzeige und haelt dabei die Zaehler stimmig.
 *
 * Dieselben Schritte braucht der Autor, der etwas zurueckzieht, und die
 * Moderation, die etwas entfernt. Gespeichert wird beim Aufrufer.
 */
class ContentWithdrawal
{
    public function __construct(
        private readonly ForumStatistics $forumStatistics,
        private readonly PostRepository $postRepository
    ) {
    }

    /**
     * @return bool false, wenn der Beitrag schon zurueckgezogen war — ein zweiter
     *              Aufruf (Zurueck-Knopf, Doppelklick) wuerde die Zaehler erneut senken.
     */
    public function withdrawPost(Post $post): bool
    {
        if (!$post->getEnabled()) {
            return false;
        }

        $thread = $post->getThread();
        $board = $thread?->getCity() ?? $thread?->getBoard();

        $this->forumStatistics->disablePost($post, $board instanceof BoardInterface ? $board : null);

        $post->setEnabled(false);

        if (null !== $thread) {
            $post->getUser()?->decForumPostCount();
        }

        return true;
    }

    /**
     * @return bool false, wenn das Thema schon zurueckgezogen war.
     */
    public function withdrawThread(Thread $thread): bool
    {
        if (!$thread->getEnabled()) {
            return false;
        }

        $board = $thread->getCity() ?? $thread->getBoard();

        if ($board instanceof BoardInterface) {
            $this->forumStatistics->disableThread($thread, $board);
        }

        foreach ($this->postRepository->findPostsForThread($thread) as $post) {
            $post->getUser()?->decForumPostCount();
        }

        $thread->setEnabled(false);

        return true;
    }
}
