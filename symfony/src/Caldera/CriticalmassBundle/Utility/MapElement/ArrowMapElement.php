<?php

namespace Caldera\CriticalmassBundle\Utility\MapElement;

use \Caldera\CriticalmassBundle\Entity\Position;

class ArrowMapElement extends BaseMapElement
{
	protected $fromPosition;
	protected $toPosition;

	public function __construct(Position $fromPosition, Position $toPosition)
	{
		$this->fromPosition = $fromPosition;
		$this->toPosition = $toPosition;
	}

	public function getId()
	{
		return 'arrow-'.$this->fromPosition.'-'.$this->toPosition;
	}

	public function draw()
	{
		return array(
			'id' => $this->getId(),
			'type' => 'arrow',
			'fromPosition' => $this->fromPosition,
			'toPosition' => $this->toPosition
			);
	}
}