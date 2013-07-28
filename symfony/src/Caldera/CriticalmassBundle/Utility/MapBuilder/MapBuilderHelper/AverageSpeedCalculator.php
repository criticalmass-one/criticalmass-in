<?php

namespace Caldera\CriticalmassBundle\Utility\MapBuilder\MapBuilderHelper;

use Caldera\CriticalmassBundle\Entity\Position;
use Caldera\CriticalmassBundle\Utility as Utility;

class AverageSpeedCalculator
{
	protected $position1;
	protected $position2;

	public function __construct(Position $position1, Position $position2)
	{
		$this->position1 = $position1;
		$this->position2 = $position2;
	}

	public function getAverageSpeed()
	{
		$dc = new Utility\DistanceCalculator();
		$distance = $dc->calculateDistanceFromPositionToPosition($this->position1, $this->position2);
		$time = $this->position1->getCreationDateTime()->format('U') - $this->position2->getCreationDateTime()->format('U');

		$averageSpeed = $distance / $time;

		$averageSpeed *= 3600;

		return round($averageSpeed, 2);
	}
}