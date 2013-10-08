<?php

namespace Caldera\CriticalmassBundle\Utility\MapElement;

use \Caldera\CriticalmassBundle\Entity\Ride;

class MarkerMapElement extends BaseMapElement
{
    protected $ride;

	public function __construct(Ride $ride)
	{
        $this->ride = $ride;
	}

	public function getId()
	{
		return 'marker-'.$this->ride->getLatitude().'-'.$this->ride->getLongitude();
	}

	public function draw()
	{
		return array(
			'id' => $this->getId(),
			'type' => 'marker',
			'centerPosition' => array('latitude' => $this->ride->getLatitude(), 'longitude' => $this->ride->getLongitude())
			);
	}
}
