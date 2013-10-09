<?php

namespace Caldera\CriticalmassCoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Diese Klasse ist lediglich eine Erweiterung der Entity\Position, um bei der
 * Formulierung von Fixtures Codezeilen zu sparen, indem einige Eigenschaften
 * der Entitaet bereits hier definiert werden.
 *
 * @Orm\MappedSuperclass
 * @ORM\Table(name="position")
 */
class DefaultFixturePosition extends Position
{
	/**
	 * Bereits im Konstruktor werden die Eigenschaften, die spaeter in den Fixtu-
	 * res nicht von Bedeutung sind, mit Null initialisiert.
	 */
	public function __construct()
	{
		$this->setAccuracy(0.0);
		$this->setAltitude(0.0);
		$this->setAltitudeAccuracy(0.0);
		$this->setHeading(0.0);
		$this->setSpeed(0.0);
		$this->setTimestamp(0.0);
	}
}
