<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Board\Thread;

use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Post;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Thread;

class CityThread
{
    /**
     * @var Thread $thread
     */
    protected $thread;

    protected $posts = [];

    public function __construct()
    {

    }

    public function getCity()
    {
        return $this->thread->getCity();
    }

    public function setThread(Thread $thread)
    {
        $this->thread = $thread;

        return $this;
    }

    public function getThread()
    {
        return $this->thread;
    }

    public function setPosts($posts)
    {
        $this->posts = $posts;
    }

    public function getTitle()
    {
        return $this->thread->getTitle();
    }

    public function getDescription()
    {
        return null;
    }

    public function getPostNumber()
    {
        return count($this->posts);
    }

    public function getViewNumber()
    {
        return 0;
    }

    public function getLastPost()
    {
        /**
         * @var Post $lastPost
         */
        $lastPost = null;

        /**
         * @var Post $post
         */
        foreach ($this->posts as $post) {
            if (!$lastPost or $lastPost->getDateTime() < $post->getDateTime()) {
                $lastPost = $post;
            }
        }

        return $lastPost;
    }
}