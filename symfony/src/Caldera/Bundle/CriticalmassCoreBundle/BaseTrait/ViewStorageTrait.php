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
use Lsw\MemcacheBundle\Cache\LoggingMemcache;

trait ViewStorageTrait
{
    protected function getClassName(ViewableInterface $viewable): string
    {
        $namespaceClass = get_class($viewable);
        $namespaceParts = explode('\\', $namespaceClass);

        $className = array_pop($namespaceParts);

        return $className;
    }

    protected function countView(ViewableInterface $viewable)
    {
        /** @var LoggingMemcache $memcache */
        $memcache = $this->get('memcache.criticalmass');

        $viewStorage = $memcache->get('view_storage');

        if (!$viewStorage) {
            $viewStorage = [];
        }

        $viewDateTime = new \DateTime('now', new \DateTimeZone('UTC'));

        $view =
            [
                'className' => $this->getClassName($viewable),
                'entityId' => $viewable->getId(),
                'userId' => ($this->getUser() ? $this->getUser()->getId() : null),
                'dateTime' => $viewDateTime->format('Y-m-d H:i:s')
            ]
        ;

        $viewStorage[] = $view;
        
        $memcache->set('view_storage', $viewStorage);
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