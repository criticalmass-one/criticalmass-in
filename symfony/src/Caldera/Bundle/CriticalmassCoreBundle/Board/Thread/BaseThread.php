<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Board\Thread;


use Caldera\Bundle\CriticalmassModelBundle\Entity\Post;

abstract class BaseThread implements ThreadInterface
{
    protected $posts = [];

    public function setPosts($posts)
    {
        $this->posts = $posts;
    }

    public function getPostNumber()
    {
        return count($this->posts);
    }

    public function getUser()
    {
        return $this->posts[0]->getUser();
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