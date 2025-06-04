<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Item;

use App\Entity\Thread;

class ThreadItem extends AbstractItem
{
    public ?Thread $thread = null;

    public ?string $title = null;

    public ?string $text = null;

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
