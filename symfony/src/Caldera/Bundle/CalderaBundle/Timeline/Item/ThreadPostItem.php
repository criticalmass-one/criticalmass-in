<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item;

use Caldera\Bundle\CalderaBundle\Entity\Thread;

class ThreadPostItem extends AbstractItem
{
    /**
     * @var string $username
     */
    public $username;

    /**
     * @var Thread $thread
     */
    public $thread;

    /**
     * @var string $threadTitle
     */
    public $threadTitle;

    /**
     * @var string $text
     */
    public $text;

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Thread
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * @param Thread $thread
     */
    public function setThread($thread)
    {
        $this->thread = $thread;

        return $this;
    }

    /**
     * @return string
     */
    public function getThreadTitle()
    {
        return $this->title;
    }

    /**
     * @param string $threadTitle
     */
    public function setThreadTitle($threadTitle)
    {
        $this->threadTitle = $threadTitle;

        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

}