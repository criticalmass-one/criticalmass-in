<?php

namespace AppBundle\Traits;

use AppBundle\Entity\City;
use AppBundle\Entity\Photo;
use AppBundle\Entity\Ride;
use AppBundle\Entity\Thread;
use AppBundle\EntityInterface\ViewableInterface;
use AppBundle\Criticalmass\ViewStorage\ViewStorageCacheInterface;

/** @deprecated  */
trait ViewStorageTrait
{
    /** @deprecated  */
    protected function countView(ViewableInterface $viewable)
    {
        /** @var ViewStorageCacheInterface $viewStorage */
        $viewStorage = $this->get('AppBundle\Criticalmass\ViewStorage\ViewStorageCache');

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
     * @param City $city
     * @deprecated
     */
    protected function countCityView(City $city)
    {
        $this->countView($city);
    }
}
