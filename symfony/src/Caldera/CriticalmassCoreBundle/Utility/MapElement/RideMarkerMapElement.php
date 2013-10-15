<?php

namespace Caldera\CriticalmassCoreBundle\Utility\MapElement;

use \Caldera\CriticalmassCoreBundle\Entity\Ride;

class RideMarkerMapElement extends BaseMapElement
{
    protected $ride;

	public function __construct(Ride $ride)
	{
        $this->ride = $ride;
	}

	public function getId()
	{
		return 'ridemarker-'.$this->ride->getLatitude().'-'.$this->ride->getLongitude();
	}

	public function draw()
	{
		return array(
			'id' => $this->getId(),
			'type' => 'marker',
			'centerPosition' => array('latitude' => $this->ride->getLatitude(), 'longitude' => $this->ride->getLongitude()),
            'location' => $this->ride->getLocation(),
            'time' => $this->ride->getTime()
			);
	}
}
