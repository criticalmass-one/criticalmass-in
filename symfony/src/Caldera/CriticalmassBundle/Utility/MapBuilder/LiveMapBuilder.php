<?php

namespace Caldera\CriticalmassBundle\Utility\MapBuilder;

use Caldera\CriticalmassBundle\Utility\PositionFilterChain as PositionFilterChain;
use Caldera\CriticalmassBundle\Utility\MapElement as MapElement;
use Caldera\CriticalmassBundle\Utility\MapBuilder\MapBuilderHelper as MapBuilderHelper;
use Caldera\CriticalmassBundle\Utility as Utility;

/**
 * Implementierung eines MapBuilders, der allen Anspruechen der Aufgabenstel-
 * lung genuegt.
 */
class LiveMapBuilder extends BaseMapBuilder
{
	/**
	 * {@inheritDoc}
	 */
	public function getUserCounter()
	{
		$ucc = new MapBuilderHelper\UserCounterCalculator($this->additionalPositions);

		return $ucc->getUserCounter();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getAverageSpeed()
	{
		$asc = new MapBuilderHelper\AverageSpeedCalculator($this->mainPositions);

		return $asc->getAverageSpeed();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getZoomFactor()
	{
		$zfc = new MapBuilderHelper\ZoomFactorCalculator($this->mainPositions);

		return $zfc->getZoomFactor();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMapCenterLatitude()
	{
		$mcc = new MapBuilderHelper\MapCenterCalculator($this->mainPositions);

		return $mcc->calculateMapCenter("getLatitude");
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMapCenterLongitude()
	{
		$mcc = new MapBuilderHelper\MapCenterCalculator($this->mainPositions);

		return $mcc->calculateMapCenter("getLongitude");
	}

	/**
	 * {@inheritDoc}
	 */
	public function calculateMainPositions()
	{
		$psf = new PositionFilterChain\PositionFilterChain();

		$psf->setDoctrine($this->doctrine);
		$psf->setRide($this->ride);
		$psf->execute();

		$this->mainPositions = $psf->getPositionArray();
	}

	/**
	 * {@inheritDoc}
	 */
	public function calculateAdditionalPositions()
	{
		$psf = new PositionFilterChain\TailPositionFilterChain();

		$psf->setDoctrine($this->doctrine);
		$psf->setRide($this->ride);
		$psf->execute();

		$this->additionalPositions = $psf->getPositionArray();
	}
}
