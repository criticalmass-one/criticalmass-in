<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item;

use Caldera\Bundle\CriticalmassModelBundle\Entity\Photo;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Caldera\Bundle\CriticalmassModelBundle\Entity\User;

class RidePhotoItem extends AbstractItem
{
    /**
     * @var User $user
     */
    protected $user;

    /**
     * @var Ride $ride
     */
    protected $ride;

    /**
     * @var integer $counter
     */
    protected $counter;

    /**
     * @var Photo $previewPhoto
     */
    protected $previewPhoto;

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return Ride
     */
    public function getRide()
    {
        return $this->ride;
    }

    /**
     * @param Ride $ride
     */
    public function setRide($ride)
    {
        $this->ride = $ride;
    }

    /**
     * @return integer
     */
    public function getCounter()
    {
        return $this->counter;
    }

    /**
     * @param integer $counter
     */
    public function setCounter($counter)
    {
        $this->counter = $counter;
    }

    /**
     * @param Photo $previewPhoto
     */
    public function setPreviewPhoto(Photo $previewPhoto)
    {
        $this->previewPhoto = $previewPhoto;
    }

    /**
     * @return Photo
     */
    public function getPreviewPhoto()
    {
        return $this->previewPhoto;
    }
}