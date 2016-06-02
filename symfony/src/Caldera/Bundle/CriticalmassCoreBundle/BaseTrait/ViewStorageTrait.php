<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\BaseTrait;

use Caldera\Bundle\CalderaBundle\Entity\City;
use Caldera\Bundle\CalderaBundle\Entity\Event;
use Caldera\Bundle\CalderaBundle\Entity\Photo;
use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Caldera\Bundle\CalderaBundle\Entity\Thread;
use Caldera\Bundle\CalderaBundle\EntityInterface\ViewableInterface;

trait ViewStorageTrait
{
    protected function countView(ViewableInterface $viewable, $identifier)
    {
        $memcache = $this->get('memcache.criticalmass');

        $additionalViews = $memcache->get($identifier.$viewable->getId().'_additionalviews');

        if (!$additionalViews) {
            $additionalViews = 1;
        } else {
            ++$additionalViews;
        }

        $viewDateTime = new \DateTime('now', new \DateTimeZone('UTC'));

        $viewArray =
            [
                'entityId' => $viewable->getId(),
                'userId' => ($this->getUser() ? $this->getUser()->getId() : null),
                'dateTime' => $viewDateTime->format('Y-m-d H:i:s')
            ]
        ;

        $memcache->set($identifier.$viewable->getId().'_additionalviews', $additionalViews);
        $memcache->set($identifier.$viewable->getId().'_view'.$additionalViews, $viewArray);
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
}