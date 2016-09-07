<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\BaseTrait;

use Caldera\Bundle\CalderaBundle\Entity\BlogPost;
use Caldera\Bundle\CalderaBundle\Entity\City;
use Caldera\Bundle\CalderaBundle\Entity\Event;
use Caldera\Bundle\CalderaBundle\Entity\Photo;
use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Caldera\Bundle\CalderaBundle\Entity\Thread;
use Caldera\Bundle\CalderaBundle\EntityInterface\ViewableInterface;
use Lsw\MemcacheBundle\Cache\LoggingMemcache;
use Memcached;

trait ViewStorageTrait
{
    protected function countView(ViewableInterface $viewable, string $identifier)
    {
        //$memcache = $this->get('memcache.criticalmass');

        $memcache = new Memcached();
        $memcache->addServer('localhost', 11211);

        $viewDateTime = new \DateTime('now', new \DateTimeZone('UTC'));

        $view = [
            'entityId' => $viewable->getId(),
            'userId' => ($this->getUser() ? $this->getUser()->getId() : null),
            'dateTime' => $viewDateTime->format('Y-m-d H:i:s')
        ];
        
        do {
            $cas = null;

            $serializedViews = $memcache->get($identifier.'_views', null, $cas);

            if ($memcache->getResultCode() == Memcached::RES_NOTFOUND) {
                $views = [$view];

                $memcache->add($identifier.'_views', serialize($views));
            } else {
                $views = unserialize($serializedViews);

                $views[] = $view;

                $memcache->cas($identifier.'_views', serialize($views), $cas);
            }
        } while ($memcache->getResultCode() != Memcached::RES_SUCCESS);
    }

    protected function countThreadView(Thread $thread)
    {
        $this->countView($thread, 'thread');
    }

    protected function countPhotoView(Photo $photo)
    {
        $this->countView($photo, 'photo');
    }

    protected function countRideView(Ride $ride)
    {
        $this->countView($ride, 'ride');
    }

    protected function countEventView(Event $event)
    {
        $this->countView($event, 'event');
    }

    protected function countCityView(City $city)
    {
        $this->countView($city, 'city');
    }

    protected function countBlogPostView(BlogPost $blogPost)
    {
        $this->countView($blogPost, 'blogPost');
    }
}