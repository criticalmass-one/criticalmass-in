<?php

namespace Caldera\CriticalmassCoreBundle\Utility\MapBuilder;

use Caldera\CriticalmassCoreBundle\Utility\PositionFilterChain as PositionFilterChain;
use Caldera\CriticalmassCoreBundle\Utility\MapElement as MapElement;
use Caldera\CriticalmassCoreBundle\Utility\MapBuilder\MapBuilderHelper as MapBuilderHelper;
use Caldera\CriticalmassCoreBundle\Utility as Utility;

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
		$ucc = new MapBuilderHelper\UserCounterCalculator($this->additionalPositions, $this->ride);

		return $ucc->getUserCounter();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getAverageSpeed()
	{
		$asc = new MapBuilderHelper\AverageSpeedCalculator($this->mainPositions, $this->ride);

		return $asc->getAverageSpeed();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getZoomFactor()
	{
		$zfc = new MapBuilderHelper\ZoomFactorCalculator($this->mainPositions, $this->ride);

		return $zfc->getZoomFactor();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMapCenterLatitude()
	{
		$mcc = new MapBuilderHelper\MapCenterCalculator($this->mainPositions, $this->ride);

		return $mcc->getMapCenterLatitude();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMapCenterLongitude()
	{
		$mcc = new MapBuilderHelper\MapCenterCalculator($this->mainPositions, $this->ride);

		return $mcc->getMapCenterLongitude();
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
