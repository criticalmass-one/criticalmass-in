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

	public function getId()
	{
		return 'circle-'.$this->centerPosition->getLatitude().'-'.$this->centerPosition->getLongitude().'-'.$this->radius;
	}

	public function draw()
	{
		return array(
			'id' => $this->getId(),
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