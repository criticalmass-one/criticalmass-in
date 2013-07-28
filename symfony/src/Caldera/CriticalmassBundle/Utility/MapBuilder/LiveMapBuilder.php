<?php

namespace Caldera\CriticalmassBundle\Utility\MapBuilder;

use Caldera\CriticalmassBundle\Utility\PositionFilter as PositionFilter;
use Caldera\CriticalmassBundle\Utility\MapElement as MapElement;
use Caldera\CriticalmassBundle\Utility\MapBuilder\MapBuilderHelper as MapBuilderHelper;
use Caldera\CriticalmassBundle\Utility as Utility;

class LiveMapBuilder extends BaseMapBuilder
{
	public function getUserCounter()
	{
		$ucc = new MapBuilderHelper\UserCounterCalculator($this->additionalPositions);

		return $ucc->getUserCounter();
	}

	public function getAverageSpeed()
	{
		$asc = new MapBuilderHelper\AverageSpeedCalculator($this->mainPositions[0], $this->mainPositions[1]);

		return $asc->getAverageSpeed();
	}

	public function getZoomFactor()
	{
		$zfc = new MapBuilderHelper\ZoomFactorCalculator($this->mainPositions[0], $this->mainPositions[1]);

		return $zfc->getZoomFactor();
	}

	public function getMapCenterLatitude()
	{
		$mcc = new MapBuilderHelper\MapCenterCalculator($this->mainPositions[0], $this->mainPositions[1]);

		return $mcc->calculateMapCenter("getLatitude");
	}

	public function getMapCenterLongitude()
	{
		$mcc = new MapBuilderHelper\MapCenterCalculator($this->mainPositions[0], $this->mainPositions[1]);

		return $mcc->calculateMapCenter("getLongitude");
	}

	public function calculateMainPositions()
	{
		$psf = new PositionFilter\PositionFilterChain();
		$this->mainPositions = $psf->setRide($this->ride)->setPositions($this->positions)->execute()->getPositions();
	}

	public function calculateAdditionalPositions()
	{
		$psf = new PositionFilter\TailFilterChain();
		$this->additionalPositions = $psf->setRide($this->ride)->setPositions($this->positions)->execute()->getPositions();
	}
}
