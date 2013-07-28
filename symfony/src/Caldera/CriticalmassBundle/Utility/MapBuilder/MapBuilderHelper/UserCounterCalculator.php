<?php

namespace Caldera\CriticalmassBundle\Utility\MapBuilder\MapBuilderHelper;

use Caldera\CriticalmassBundle\Entity\Position;
use Caldera\CriticalmassBundle\Utility as Utility;

class UserCounterCalculator
{
	protected $positions = array();

	public function __construct($positions)
	{
		$this->positions = $positions;
	}

	public function getUserCounter()
	{
		$users = array();

		foreach ($this->positions as $position)
		{
			if (!in_array($position->getUser(), $users))
			{
				$users[] = $position->getUser();
			}
		}

		return count($users);
	}
}
