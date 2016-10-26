<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\BaseTrait;

use Caldera\Bundle\CalderaBundle\Entity\BlogPost;
use Caldera\Bundle\CalderaBundle\Entity\City;
use Caldera\Bundle\CalderaBundle\Entity\Content;
use Caldera\Bundle\CalderaBundle\Entity\Event;
use Caldera\Bundle\CalderaBundle\Entity\Photo;
use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Caldera\Bundle\CalderaBundle\Entity\Thread;
use Caldera\Bundle\CalderaBundle\EntityInterface\ViewableInterface;
use Caldera\Bundle\CalderaBundle\ViewStorage\ViewStorageInterface;

trait ViewStorageTrait
{
    protected function countView(ViewableInterface $viewable)
    {
        /** @var ViewStorageInterface $viewStorage */
        $viewStorage = $this->get('caldera.view_storage');

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