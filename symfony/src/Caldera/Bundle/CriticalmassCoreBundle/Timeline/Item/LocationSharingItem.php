<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item;

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
     * @return string
     */
    public function getPolyline()
    {
        return $this->polyline;
    }

    /**
     * @param string $polyline
     */
    public function setPolyline($polyline)
    {
        $this->polyline = $polyline;
    }
}