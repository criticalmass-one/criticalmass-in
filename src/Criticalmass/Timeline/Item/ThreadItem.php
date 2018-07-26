<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Item;

use App\Entity\Thread;

class ThreadItem extends AbstractItem
{
    /** @var Thread $thread */
    public $thread;

    /** @var string $title */
    public $title;

    /** @var string $text */
    public $text;

    public function getThread(): Thread
    {
        return $this->thread;
    }

    public function setThread(Thread $thread): ThreadItem
    {
        $this->thread = $thread;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): ThreadItem
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): ThreadItem
    {
        $this->text = $text;

        return $this;
    }

}
