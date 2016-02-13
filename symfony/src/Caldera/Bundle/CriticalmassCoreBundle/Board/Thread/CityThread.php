<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Board\Thread;

use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Thread;

class CityThread extends BaseThread
{
    /**
     * @var Thread $thread
     */
    protected $thread;

    public function setThread(Thread $thread)
    {
        $this->thread = $thread;

        return $this;
    }

    public function getThread()
    {
        return $this->thread;
    }

    public function getCity()
    {
        return $this->thread->getCity();
    }

    public function setCity(City $city)
    {
        $this->thread->setCity($city);

        return $this;
    }

    public function getTitle()
    {
        return $this->thread->getTitle();
    }

    public function getViewNumber()
    {
        return 0;
    }
}