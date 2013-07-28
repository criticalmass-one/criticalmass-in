<?php

namespace Caldera\CriticalmassBundle\Utility\MapElement;

use \Caldera\CriticalmassBundle\Entity\Position;

class CircleMapElement
{
	protected $centerPosition;
	protected $radius;

	public function __construct(Position $centerPosition, Position $radius)
	{
		$this->centerPosition = $centerPosition;
		$this->radius = $radius;
	}

	public function draw()
	{
		return array(
			'id' => 'circle-'.$this->centerPosition.'-'.$this->radius,
			'type' => 'circle',
			'latitude' => $this->centerPosition->getLatitude(),
			'longitude' => $this->centerPosition->getLongitude(),
			'radius' => $this->radius,
			'strokeColor' => '#ff0000',
			'fillColor' => '#ff0000',
			'strokeOpacity' => 0.8,
			'fillOpacity' => 0.35,
			'strokeWeight' => 2.0
			);
		)
	}
}