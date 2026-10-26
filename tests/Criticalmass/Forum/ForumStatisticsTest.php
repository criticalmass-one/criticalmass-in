<?php declare(strict_types=1);

namespace Tests\Criticalmass\Forum;

use App\Criticalmass\Forum\ForumStatistics;
use App\Entity\Board;
use App\Entity\Post;
use App\Entity\Thread;
use App\Repository\PostRepository;
use App\Repository\ThreadRepository;
use PHPUnit\Framework\TestCase;

class ForumStatisticsTest extends TestCase
{
    private function board(string $title, int $threads, int $posts): Board
    {
        $board = new Board();
        $board->setTitle($title)->setThreadNumber($threads)->setPostNumber($posts);

        return $board;
    }

    private function statistics(int $postsInThread = 3, ?Thread $latestThread = null, ?Post $latestPost = null): ForumStatistics
    {
        $postRepository = $this->createMock(PostRepository::class);
        $postRepository->method('countPostsForThread')->willReturn($postsInThread);
        $postRepository->method('findLatestPostForThread')->willReturn($latestPost);

        $threadRepository = $this->createMock(ThreadRepository::class);
        $threadRepository->method('findLatestThread')->willReturn($latestThread);

        return new ForumStatistics($postRepository, $threadRepository);
    }

    public function testMovingAThreadShiftsBothCounters(): void
    {
        $source = $this->board('Quelle', 5, 20);
        $target = $this->board('Ziel', 2, 7);
        $thread = new Thread();

        $this->statistics(3)->moveThread($thread, $source, $target);

        self::assertSame(4, $source->getThreadNumber());
        self::assertSame(17, $source->getPostNumber());
        self::assertSame(3, $target->getThreadNumber());
        self::assertSame(10, $target->getPostNumber());
        self::assertSame($thread, $target->getLastThread());
    }

    public function testMovingAwayTheLastThreadPromotesTheNextOne(): void
    {
        $thread = new Thread();
        $successor = new Thread();

        $source = $this->board('Quelle', 2, 8);
        $source->setLastThread($thread);

        $this->statistics(2, $successor)->moveThread($thread, $source, $this->board('Ziel', 0, 0));

        self::assertSame($successor, $source->getLastThread());
    }

    public function testMovingAnUnrelatedThreadLeavesTheLastThreadAlone(): void
    {
        $other = new Thread();
        $source = $this->board('Quelle', 3, 9);
        $source->setLastThread($other);

        $this->statistics(1, new Thread())->moveThread(new Thread(), $source, $this->board('Ziel', 0, 0));

        self::assertSame($other, $source->getLastThread(), 'Nur das entfernte Thema wird ersetzt.');
    }

    public function testDisablingAThreadRemovesItFromTheCounters(): void
    {
        $board = $this->board('Forum', 4, 12);
        $thread = new Thread();

        $this->statistics(5)->disableThread($thread, $board);

        self::assertSame(3, $board->getThreadNumber());
        self::assertSame(7, $board->getPostNumber());
    }

    public function testCountersNeverFallBelowZero(): void
    {
        $board = $this->board('Leeres Forum', 0, 1);

        $this->statistics(9)->disableThread(new Thread(), $board);

        self::assertSame(0, $board->getThreadNumber());
        self::assertSame(0, $board->getPostNumber(), 'Ein Zähler darf nicht negativ werden.');
    }

    public function testDisablingAPostDecrementsThreadAndBoard(): void
    {
        $board = $this->board('Forum', 1, 6);
        $thread = new Thread();
        $thread->setPostNumber(4);

        $post = new Post();
        $post->setThread($thread);

        $this->statistics()->disablePost($post, $board);

        self::assertSame(3, $thread->getPostNumber());
        self::assertSame(5, $board->getPostNumber());
    }

    public function testDisablingTheLastPostPromotesThePreviousOne(): void
    {
        $thread = new Thread();
        $thread->setPostNumber(2);

        $post = new Post();
        $post->setThread($thread);
        $thread->setLastPost($post);

        $predecessor = new Post();

        $this->statistics(2, null, $predecessor)->disablePost($post, $this->board('Forum', 1, 3));

        self::assertSame($predecessor, $thread->getLastPost());
    }

    public function testDisablingAPostOutsideAThreadOnlyTouchesTheBoard(): void
    {
        $board = $this->board('Forum', 1, 4);

        // Kommentare an Touren, Städten und Fotos hängen an keinem Thema.
        $this->statistics()->disablePost(new Post(), $board);

        self::assertSame(3, $board->getPostNumber());
    }
}
