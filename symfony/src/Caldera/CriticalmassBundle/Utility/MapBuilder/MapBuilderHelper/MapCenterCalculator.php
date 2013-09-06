<?php

namespace Caldera\CriticalmassBundle\Utility\MapBuilder\MapBuilderHelper;

use Caldera\CriticalmassBundle\Entity\Position;
use Caldera\CriticalmassBundle\Utility as Utility;

/**
 * Diese Hilfsklasse bestimmt den Mittelpunkt eines eingebetteten Kartenaus-
 * schnittes anhand der uebergebenen Positionsdaten.
 */
class MapCenterCalculator extends BaseMapBuilderHelper
{
	/**
	 * Gibt den berechneten Breitengrad zurueck.
	 *
	 * @return Float: Breitengrad des Mittelpunktes
	 */
	public function getMapCenterLatitude()
	{
		return $this->calculateMapCenter("getLatitude");
	}

	/**
	 * Gibt den berechneten Laengengrad zurueck.
	 *
	 * @return Float: Laengengrad des Mittelpunktes
	 */
	public function getMapCenterLongitude()
	{
		return $this->calculateMapCenter("getLongitude");
	}

	/**
	 * Berechnet je nach Parameter den Breiten- oder den Laengengrad des Mittel-
	 * punktes.
	 *
	 * @param String: Angabe, ob der Breiten- oder der Laengengrad bestimmt werden
	 * soll
	 */
	public function calculateMapCenter($coordinateFunction)
	{
		$min = null;
		$max = null;

		foreach ($this->positionArray->getPositions() as $position)
		{
			if (!isset($min) && !isset($max))
			{
				$min = $position->$coordinateFunction();
				$max = $position->$coordinateFunction();
			}
			elseif ($min > $position->$coordinateFunction())
			{
				$min = $position->$coordinateFunction();
			}
			elseif ($max < $position->$coordinateFunction())
			{
				$max = $position->$coordinateFunction();
			}
		}

		return $min + ($max - $min) / 2.0;
	}
}