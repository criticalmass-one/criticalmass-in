<?php

namespace Caldera\CriticalmassBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @Orm\MappedSuperclass
 * @ORM\Table(name="position")
 */
class DefaultFixturePosition extends Position
{
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
