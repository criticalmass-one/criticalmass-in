<?php

namespace Caldera\CriticalmassBundle\Utility\MapBuilder\MapBuilderHelper;

use Caldera\CriticalmassBundle\Entity\Position;
use Caldera\CriticalmassBundle\Utility as Utility;

/**
 * Bestimmt die Anzahl der Benutzer, deren Positionsdaten in die Berechnung
 * eingeflossen sind.
 */
class UserCounterCalculator extends BaseMapBuilderHelper
{
	/**
	 * Gibt die Anzahl der an der Berechnung beteiligten Benutzer zurueck.
	 *
	 * @return Integer: Anzahl der Benutzer
	 */
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
