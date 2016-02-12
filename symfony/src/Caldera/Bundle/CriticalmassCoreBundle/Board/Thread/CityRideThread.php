<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Board\Thread;

use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Post;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;

class CityRideThread
{
    /**
     * @var City $city
     */
    protected $city;

    /**
     * @var Ride $ride
     */
    protected $ride;

    protected $posts = [];

    public function __construct()
    {

    }

    public function setRide(Ride $ride)
    {
        $this->ride = $ride;

        return $this;
    }

    public function getRide()
    {
        return $this->ride;
    }

    public function setPosts($posts)
    {
        $this->posts = $posts;
    }

    public function getTitle()
    {
        return 'Kommentare zur Tour am ' . $this->ride->getDateTime()->format('d.m.Y');
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