<?php

namespace Caldera\CriticalmassBundle\Utility\PositionFilter;

use Caldera\CriticalmassBundle\Entity as Entity;

class PositionArray
{
	protected $positions = array();

	public function __construct($positions)
	{
		$this->positions = $positions;
	}

	public function getPositions()
	{
		return $this->positions;
	}

	public function setPositions($positions)
	{
		$this->positions = $positions;
	}

	public function getPosition($key)
	{
		return $this->positions[$key];
	}

	public function setPosition($key, Entity\Position $position)
	{
		$this->positions[$key] = $position;
	}

	public function addPosition(Entity\Position $position)
	{
		$this->positions[] = $position;
	}

	public function deletePosition($key)
	{
		unset($this->positions[$key]);
	}
}