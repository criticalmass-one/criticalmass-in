<?php

namespace Caldera\CriticalmassCoreBundle\Utility\MapElement;

use \Caldera\CriticalmassCoreBundle\Entity\Position;

class PositionMarkerMapElement extends BaseMapElement
{
    protected $position;

	public function __construct(Position $position)
	{
        $this->position = $position;
	}

	public function getId()
	{
		return 'positionmarker-'.$this->position->getLatitude().'-'.$this->position->getLongitude();
	}

	public function draw()
	{
		return array(
			'id' => $this->getId(),
			'type' => 'marker',
			'centerPosition' => array('latitude' => $this->position->getLatitude(), 'longitude' => $this->position->getLongitude())
			);
	}
}
