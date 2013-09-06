<?php

namespace Caldera\CriticalmassBundle\Utility\MapBuilder\MapBuilderHelper;

use Caldera\CriticalmassBundle\Entity\Position;
use Caldera\CriticalmassBundle\Utility as Utility;

/**
 * Berechnet anhand der enthaltenen Positionsdaten den Zoomfaktor des eingebet-
 * teten Kartenausschnittes.
 */
class ZoomFactorCalculator extends BaseMapBuilderHelper
{
	/**
	 * Gibt den Zoomfaktor der eingebetteten Karte zurueck. Dazu werden zunaechst
	 * die Kanten eines Rechtecks bestimmt, das alle Positionen umschliesst, um 
	 * daraus die Ausdehnung der Flaeche und daraus resultierend den Zoom-Faktor
	 * zu bestimmen.
	 *
	 * @return Integer: Zoomfaktor der Karte
	 */
	public function getZoomFactor()
	{
		$minX = null;
		$maxX = null;
		$minY = null;
		$maxY = null;

		// alle Positionsdaten durchlaufen
		foreach ($this->positionArray->getPositions() as $position)
		{
			// handelt es sich um das erste Datum?
			if (!isset($minX) && !isset($maxX) && !isset($minY) && !isset($maxY))
			{
				$minX = $position->getLatitude();
				$maxX = $position->getLatitude();
				$minY = $position->getLongitude();
				$maxY = $position->getLongitude();
			}
			else
			{
				// neues horizontales Minimum?
				if ($minX > $position->getLatitude())
				{
					$minX = $position->getLatitude();
				}

				// neues horizontales Maximum?
				if ($maxX < $position->getLatitude())
				{
					$maxX = $position->getLatitude();
				}

				// neues vertikales Minimum?
				if ($minY > $position->getLongitude())
				{
					$minY = $position->getLongitude();
				}

				// neues vertikales Maximum?
				if ($maxY < $position->getLongitude())
				{
					$maxY = $position->getLongitude();
				}
			}
		}

		// Ausdehnung bestimmen?
		$distanceX = $maxX - $minX;
		$distanceY = $maxY - $minY;

		// Distanz der Flaeche berechnen
		$distance = ($distanceX > $distanceY ? $distanceX : $distanceY) + 0.001;

		// Zoom-Faktor fuer Google Maps bestimmen
		$zoomFactor = floor(log(960 * 360 / $distance / 256)) + 1;

		return $zoomFactor;
	}
}