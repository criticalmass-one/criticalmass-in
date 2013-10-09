<?php

namespace Caldera\CriticalmassCoreBundle\Utility\MapBuilder\MapBuilderHelper;

use Caldera\CriticalmassCoreBundle\Utility\PositionArray as PositionArray;
use Caldera\CriticalmassCoreBundle\Entity\Ride as Ride;

/**
 * Ein MapBuilderHelper ist ein Untermodul des MapBuilders und fuer die Berech-
 * nung einer bestimmten Information zustaendig. Die Berechnung wird in einer
 * solchen Klasse gekapselt und ist beliebig austauschbar.
 */
abstract class BaseMapBuilderHelper
{
	/**
	 * Speichert das PositionArray mit allen Positionsdatum ab.
	 */
	protected $positionArray;

    protected $ride;

	/**
	 * Konstruiert einen MapBuilderHelper, der ein PositionArray zur Bearbeitung
	 * erhaelt.
	 *
	 * @param PositionArray $positionArray: Array mit den Positionsdaten
	 */
	public function __construct(PositionArray $positionArray, Ride $ride)
	{
		$this->positionArray = $positionArray;
        $this->ride = $ride;
	}
}