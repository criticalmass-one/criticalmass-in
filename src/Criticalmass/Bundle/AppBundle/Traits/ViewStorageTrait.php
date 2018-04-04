<?php

namespace Criticalmass\Bundle\AppBundle\Traits;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\Photo;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Bundle\AppBundle\Entity\Thread;
use Criticalmass\Bundle\AppBundle\EntityInterface\ViewableInterface;
use Criticalmass\Component\ViewStorage\ViewStorageCacheInterface;

/** @deprecated  */
trait ViewStorageTrait
{
    /** @deprecated  */
    protected function countView(ViewableInterface $viewable)
    {
        /** @var ViewStorageCacheInterface $viewStorage */
        $viewStorage = $this->get('Criticalmass\Component\ViewStorage\ViewStorageCache');

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
