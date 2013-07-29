<?php

namespace Caldera\CriticalmassBundle\Utility\MapBuilder\MapBuilderHelper;

use Caldera\CriticalmassBundle\Entity\Position;
use Caldera\CriticalmassBundle\Utility as Utility;

class UserCounterCalculator extends BaseMapBuilderHelper
{
	public function getUserCounter()
	{
		$users = array();

		foreach ($this->positionArray->getPositions() as $position)
		{
			if (!in_array($position->getUser(), $users))
			{
				$users[] = $position->getUser();
			}
		}

		return count($users);
	}
}
