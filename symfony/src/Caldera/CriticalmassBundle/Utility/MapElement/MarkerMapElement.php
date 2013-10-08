<?php

namespace Caldera\CriticalmassBundle\Utility\MapElement;

use \Caldera\CriticalmassBundle\Entity\Position;

class MarkerMapElement extends BaseMapElement
{
    protected $centerPosition;

	public function __construct(Position $centerPosition)
	{
        $this->centerPosition = $centerPosition;
	}

	public function getId()
	{
		return 'marker-'.$this->centerPosition->getLatitude().'-'.$this->centerPosition->getLongitude();
	}

	public function draw()
	{
		return array(
			'id' => $this->getId(),
			'type' => 'marker',
			'centerPosition' => array('latitude' => $this->centerPosition->getLatitude(), 'longitude' => $this->centerPosition->getLongitude())
			);
	}
}
