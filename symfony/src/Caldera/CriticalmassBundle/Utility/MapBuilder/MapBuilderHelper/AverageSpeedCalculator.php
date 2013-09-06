<?php

namespace Caldera\CriticalmassBundle\Utility\MapBuilder\MapBuilderHelper;

use Caldera\CriticalmassBundle\Entity\Position;
use Caldera\CriticalmassBundle\Utility as Utility;

/**
 * Berechnet die Durchschnittsgeschwindigkeit des Teilnehmerfeldes.
 */
class AverageSpeedCalculator extends BaseMapBuilderHelper
{
	/**
	 * Gibt die berechnete Durchschnittsgeschwindigkeit zurueck.
	 *
	 * @return Float: Durchschnittsgeschwindigkeit des Teilnehmerfeldes
	 */
	public function getAverageSpeed()
	{
		// liegen ueberhaupt genuegend Positionen zur Berechnung vor?
		if ($this->positionArray->countPositions() < 2)
		{
			return 0;
		}

		// Abstand der Positionsdaten bestimmen
		$dc = new Utility\DistanceCalculator();
		$distance = $dc->calculateDistanceFromPositionToPosition($this->positionArray->getPosition(0), $this->positionArray->getPosition(1));

		// zeitliche Differenz berechnen
		$time = $this->positionArray->getPosition(0)->getCreationDateTime()->format('U') - 
						$this->positionArray->getPosition(1)->getCreationDateTime()->format('U');

		// Durchschnittsgeschwindigkeit berechnen
		$averageSpeed = $distance / $time;

		// in Kilometer pro Stunde umrechnen
		$averageSpeed *= 3600;

		// leicht gerundet zurueckgeben
		return round($averageSpeed, 2);
	}
}