<?php declare(strict_types=1);

namespace App\Criticalmass\Forum;

use App\Entity\Post;
use App\Entity\Thread;
use App\EntityInterface\BoardInterface;
use App\Repository\PostRepository;
use App\Repository\ThreadRepository;

/**
 * Hält die Zähler und Verweise der Foren in Ordnung, wenn Themen wandern oder verschwinden.
 *
 * Board und City führen threadNumber, postNumber und lastThread als Momentaufnahme mit;
 * ohne Pflege zeigen sie nach dem Verschieben oder Deaktivieren auf Themen, die dort
 * nicht mehr liegen.
 */
class ForumStatistics
{
    public function __construct(
        private readonly PostRepository $postRepository,
        private readonly ThreadRepository $threadRepository
    ) {
    }

    public function moveThread(Thread $thread, BoardInterface $source, BoardInterface $target): void
    {
        $postCount = $this->postRepository->countPostsForThread($thread);

        $source->decThreadNumber();
        $source->decPostNumber($postCount);

        $target->incThreadNumber();
        $target->incPostNumber($postCount);
        $target->setLastThread($thread);

        $this->forgetThread($source, $thread);
    }

    public function disableThread(Thread $thread, BoardInterface $board): void
    {
        $board->decThreadNumber();
        $board->decPostNumber($this->postRepository->countPostsForThread($thread));

        $this->forgetThread($board, $thread);
    }

    public function disablePost(Post $post, ?BoardInterface $board): void
    {
        $thread = $post->getThread();

        if (null !== $thread) {
            $thread->decPostNumber();

            if ($thread->getLastPost() === $post) {
                $thread->setLastPost($this->postRepository->findLatestPostForThread($thread, $post));
            }
        }

        $board?->decPostNumber();
    }

    /**
     * Zeigt das Forum noch auf das entfernte Thema, tritt das nächstjüngere an seine Stelle.
     */
    private function forgetThread(BoardInterface $board, Thread $thread): void
    {
        if ($board->getLastThread() !== $thread) {
            return;
        }

        $board->setLastThread($this->threadRepository->findLatestThread($board, $thread));
    }
}
