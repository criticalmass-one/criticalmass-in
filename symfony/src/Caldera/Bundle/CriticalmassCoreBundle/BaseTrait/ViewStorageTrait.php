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
        /** LoggingMemcache $memcache */
        //$memcache = $this->get('memcache.criticalmass');

        $memcache = new Memcached();
        $memcache->addServer('localhost', 11211);

        $viewDateTime = new \DateTime('now', new \DateTimeZone('UTC'));

        $view = [
            'entityId' => $viewable->getId(),
            'userId' => ($this->getUser() ? $this->getUser()->getId() : null),
            'dateTime' => $viewDateTime->format('Y-m-d H:i:s')
        ];

        $serializedViews = $memcache->get($identifier.'_views');

        if (!$serializedViews) {
            $views = [
                uniqid() => $view
            ];

            $memcache->add($identifier.'_views', serialize($views));
        } else {
            $views = unserialize($serializedViews);

            $views[uniqid()] = $view;

            $memcache->set($identifier . '_views', serialize($views));
        }

            // It looks like cas is broken in PHP 7: https://github.com/php-memcached-dev/php-memcached/issues/159
/*
        do {
            $cas = 0;

            $serializedViews = $memcache->get($identifier.'_views', null, $cas);

            if (!$serializedViews) {
                $views = [
                    uniqid() => $view
                ];

                $memcache->add($identifier.'_views', serialize($views));
            } else {
                $views = unserialize($serializedViews);

                print_r($views);

                echo "<br />";
                $views[uniqid()] = $view;

                print_r($views);
                $memcache->cas($cas, $identifier.'_views', serialize($views));
            }
        } while ($memcache->getResultCode() != Memcached::RES_SUCCESS);
*/
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