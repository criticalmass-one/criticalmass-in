<?php

namespace Caldera\CriticalmassBundle\Utility\MapBuilder;

use Caldera\CriticalmassBundle\Utility\PositionFilterChain as PositionFilterChain;
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
		$asc = new MapBuilderHelper\AverageSpeedCalculator($this->mainPositions);

		return $asc->getAverageSpeed();
	}

	public function getZoomFactor()
	{
		$zfc = new MapBuilderHelper\ZoomFactorCalculator($this->mainPositions);

		return $zfc->getZoomFactor();
	}

	public function getMapCenterLatitude()
	{
		$mcc = new MapBuilderHelper\MapCenterCalculator($this->mainPositions);

		return $mcc->calculateMapCenter("getLatitude");
	}

	public function getMapCenterLongitude()
	{
		$mcc = new MapBuilderHelper\MapCenterCalculator($this->mainPositions);

		return $mcc->calculateMapCenter("getLongitude");
	}

	public function calculateMainPositions()
	{
		$psf = new PositionFilterChain\PositionFilterChain();

		$psf->setDoctrine($this->doctrine);
		$psf->setRide($this->ride);
		$psf->execute();

		$this->mainPositions = $psf->getPositionArray();
	}

	public function calculateAdditionalPositions()
	{
		$psf = new PositionFilterChain\TailPositionFilterChain();

		$psf->setDoctrine($this->doctrine);
		$psf->setRide($this->ride);
		$psf->execute();

		$this->additionalPositions = $psf->getPositionArray();
	}
}
