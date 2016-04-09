<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item;

use Caldera\Bundle\CriticalmassModelBundle\Entity\Photo;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Caldera\Bundle\CriticalmassModelBundle\Entity\User;

class LocationSharingItem extends AbstractItem
{
    /**
     * @var array $cityList
     */
    protected $cityList;

    /**
     * @var integer $sharingCounter
     */
    protected $sharingCounter;

    /**
     * @return array
     */
    public function getCityList()
    {
        return $this->cityList;
    }

    /**
     * @param array $cityList
     */
    public function setCityList(array $cityList)
    {
        $this->cityList = $cityList;
    }

    /**
     * @return int
     */
    public function getSharingCounter()
    {
        return $this->sharingCounter;
    }

    /**
     * @param int $sharingCounter
     */
    public function setSharingCounter($sharingCounter)
    {
        $this->sharingCounter = $sharingCounter;
    }
}