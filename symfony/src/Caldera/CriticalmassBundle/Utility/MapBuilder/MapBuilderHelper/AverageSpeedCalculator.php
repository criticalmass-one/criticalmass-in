<?php

namespace Caldera\CriticalmassBundle\Utility\MapBuilder\MapBuilderHelper;

use Caldera\CriticalmassBundle\Entity\Position;
use Caldera\CriticalmassBundle\Utility as Utility;

class AverageSpeedCalculator extends BaseMapBuilderHelper
{
	public function getAverageSpeed()
	{
		if ($this->positionArray->countPositions() < 2)
		{
			return 0;
		}

		$dc = new Utility\DistanceCalculator();
		$distance = $dc->calculateDistanceFromPositionToPosition($this->positionArray->getPosition(0), $this->positionArray->getPosition(1));
		
		$time = $this->positionArray->getPosition(0)->getCreationDateTime()->format('U') - 
						$this->positionArray->getPosition(1)->getCreationDateTime()->format('U');

		$averageSpeed = $distance / $time;

		$averageSpeed *= 3600;

		return round($averageSpeed, 2);
	}
}