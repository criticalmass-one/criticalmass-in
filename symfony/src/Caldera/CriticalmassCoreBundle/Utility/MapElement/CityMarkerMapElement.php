<?php

namespace Caldera\CriticalmassCoreBundle\Utility\MapElement;

use \Caldera\CriticalmassCoreBundle\Entity\City;

class CityMarkerMapElement extends BaseMapElement
{
    protected $position;

	public function __construct(City $city)
	{
        $this->city = $city;
	}

	public function getId()
	{
		return 'citymarker-'.$this->city->getLatitude().'-'.$this->city->getLongitude();
	}

	public function draw()
	{
		return array(
			'id' => $this->getId(),
			'type' => 'citymarker',
            'cityTitle' => $this->city->getCity(),
            'citySlug' => $this->city->getMainSlug()->getSlug(),
			'centerPosition' => array('latitude' => $this->city->getLatitude(), 'longitude' => $this->city->getLongitude()),
            'description' => $this->city->getDescription()
			);
	}
}
