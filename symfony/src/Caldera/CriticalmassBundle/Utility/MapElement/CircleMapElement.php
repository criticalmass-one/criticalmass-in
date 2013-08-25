<?php

namespace Caldera\CriticalmassBundle\Utility\MapElement;

use \Caldera\CriticalmassBundle\Entity\Position;

class CircleMapElement extends BaseMapElement
{
	protected $centerPosition;
	protected $radius;
	protected $strokeColor = '#ff0000';
	protected $fillColor = '#ff0000';
	protected $strokeOpacity = 0.8;
	protected $fillOpacity = 0.35;
	protected $strokeWeight = 2.0;

	public function __construct(Position $centerPosition, $radius)
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
			'strokeColor' => $this->strokeColor,
			'fillColor' => $this->fillColor,
			'strokeOpacity' => $this->strokeOpacity,
			'fillOpacity' => $this->fillOpacity,
			'strokeWeight' => $this->strokeWeight
		);
	}
}