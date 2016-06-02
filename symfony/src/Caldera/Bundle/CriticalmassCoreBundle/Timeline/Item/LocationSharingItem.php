<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item;

use Caldera\Bundle\CalderaBundle\Entity\Photo;
use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Caldera\Bundle\CalderaBundle\Entity\User;

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
     * @var string $polyline
     */
    protected $polyline;
    
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


    /**
     * @param string $polyline
     */
    public function setPolyline($polyline)
    {
        $this->polyline = $polyline;
    }

    /**
     * @return string
     */
    public function getPolyline()
    {
        return $this->polyline;
    }
}