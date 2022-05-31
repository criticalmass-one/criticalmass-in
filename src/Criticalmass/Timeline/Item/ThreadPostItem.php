<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Item;

use App\Entity\Thread;

class ThreadPostItem extends AbstractItem
{
    protected ?int $postId = null;

    protected ?Thread $thread = null;

    protected ?string $threadTitle = null;

    protected ?string $text = null;

    public function getPostId(): int
    {
        return $this->postId;
    }

    public function setPostId(int $postId): ThreadPostItem
    {
        $this->postId = $postId;

        return $this;
    }

    public function getThread(): Thread
    {
        return $this->thread;
    }

    public function setThread(Thread $thread): ThreadPostItem
    {
        $this->thread = $thread;

        return $this;
    }

    public function getThreadTitle(): string
    {
        return $this->threadTitle;
    }

    public function setThreadTitle(string $threadTitle): ThreadPostItem
    {
        $this->threadTitle = $threadTitle;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): ThreadPostItem
    {
        $this->text = $text;

        return $this;
    }
}
