<?php

namespace AppBundle\Criticalmass\Timeline\Item;

use AppBundle\Entity\Thread;

class ThreadPostItem extends AbstractItem
{
    /** @var string $username */
    public $username;

    /** @var Thread $thread */
    public $thread;

    /** @var string $threadTitle */
    public $threadTitle;

    /** @var string $text */
    public $text;

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): ThreadPostItem
    {
        $this->username = $username;

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
