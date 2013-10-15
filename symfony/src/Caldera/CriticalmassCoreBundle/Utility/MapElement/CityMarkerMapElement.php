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
			'type' => 'marker',
			'centerPosition' => array('latitude' => $this->city->getLatitude(), 'longitude' => $this->city->getLongitude()),
            'description' => $city->getDescription()
			);
	}
}
