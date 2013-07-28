<?php

namespace Caldera\CriticalmassBundle\Utility\MapElement;

use \Caldera\CriticalmassBundle\Entity\Position;

class ArrowMapElement
{
	protected $fromPosition;
	protected $toPosition;

	public function __construct(Position $fromPosition, Position $toPosition)
	{
		$this->fromPosition = $fromPosition;
		$this->toPosition = $toPosition;
	}

	public function draw()
	{
		return array(
			'id' => 'arrow-'.$this->fromPosition.'-'.$this->toPosition,
			'type' => 'arrow',
			'fromPosition' => $this->fromPosition,
			'toPosition' => $this->toPosition
			);
		)
	}
}