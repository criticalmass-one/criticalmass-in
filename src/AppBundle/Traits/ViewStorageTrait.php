<?php

namespace AppBundle\Traits;

use AppBundle\Entity\BlogPost;
use AppBundle\Entity\City;
use AppBundle\Entity\Content;
use AppBundle\Entity\Event;
use AppBundle\Entity\Photo;
use AppBundle\Entity\Ride;
use AppBundle\Entity\Thread;
use AppBundle\EntityInterface\ViewableInterface;
use AppBundle\ViewStorage\ViewStorageCacheInterface;

trait ViewStorageTrait
{
    protected function countView(ViewableInterface $viewable)
    {
        /** @var ViewStorageCacheInterface $viewStorage */
        $viewStorage = $this->get('caldera.view_storage.cache');

        $viewStorage->countView($viewable);
    }

    /**
     * @param Thread $thread
     * @deprecated
     */
    protected function countThreadView(Thread $thread)
    {
        $this->countView($thread);
    }

    /**
     * @param Photo $photo
     * @deprecated
     */
    protected function countPhotoView(Photo $photo)
    {
        $this->countView($photo);
    }

    /**
     * @param Ride $ride
     * @deprecated
     */
    protected function countRideView(Ride $ride)
    {
        $this->countView($ride);
    }

    /**
     * @param Event $event
     * @deprecated
     */
    protected function countEventView(Event $event)
    {
        $this->countView($event);
    }

    /**
     * @param City $city
     * @deprecated
     */
    protected function countCityView(City $city)
    {
        $this->countView($city);
    }

    /**
     * @param BlogPost $blogPost
     * @deprecated
     */
    protected function countBlogPostView(BlogPost $blogPost)
    {
        $this->countView($blogPost);
    }

    /**
     * @param Content $content
     * @deprecated
     */
    protected function countContentView(Content $content)
    {
        $this->countView($content);
    }
}
