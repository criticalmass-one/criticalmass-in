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
		$ucc = new MapBuilderHelper\UserCounterCalculator($this->positionArray, $this->ride);

		return $ucc->getUserCounter();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getAverageSpeed()
	{
		$asc = new MapBuilderHelper\AverageSpeedCalculator($this->positionArray, $this->ride);

		return $asc->getAverageSpeed();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getZoomFactor()
	{
		$zfc = new MapBuilderHelper\ZoomFactorCalculator($this->positionArray, $this->ride);

		return $zfc->getZoomFactor();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMapCenterLatitude()
	{
        if ($this->ride->isRideRolling())
        {
    		$mcc = new MapBuilderHelper\MapCenterCalculator($this->positionArray, $this->ride);

    		return $mcc->getMapCenterLatitude();
        }
        else
        {
            return $this->ride->getPublicLatitude();
        }
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMapCenterLongitude()
	{
        if ($this->ride->isRideRolling())
        {
    		$mcc = new MapBuilderHelper\MapCenterCalculator($this->positionArray, $this->ride);

    		return $mcc->getMapCenterLongitude();
        }
        else
        {
            return $this->ride->getPublicLongitude();
        }
	}

	/**
	 * {@inheritDoc}
	 */
	public function calculatePositions()
	{
		$psf = new PositionFilterChain\PositionFilterChain();

		$psf->setDoctrine($this->doctrine);
		$psf->setRide($this->ride);
		$psf->execute();

        $this->positionArray->merge($psf->getPositionArray());

        $counter = 0;

        foreach ($psf->getPositionArray()->getPositions() as $position)
        {
            $circle = new MapElement\CircleMapElement($position, 100);

            $this->elements['position-'.$counter] = $circle->draw();
            ++$counter;
        }
	}

    public function calculatePermanentPositions()
    {
        $psf = new PositionFilterChain\PermanentPositionFilterChain();

        $psf->setDoctrine($this->doctrine);
        $psf->setRide($this->ride);
        $psf->execute();

        $this->positionArray->merge($psf->getPositionArray());

        foreach ($psf->getPositionArray()->getPositions() as $position)
        {
            $marker = new MapElement\PositionMarkerMapElement($position);

            $this->elements[$marker->getId()] = $marker->draw();
        }
    }

    public function additionalElements()
    {/*
        if ($this->positionArray->countPositions() > 1)
        {
            $arrow = new MapElement\ArrowMapElement($positionArray[0], $positionArray[1]);
            $elements[] = $arrow->draw();
        }*/

        $this->calculatePermanentPositions();
        $marker = new MapElement\RideMarkerMapElement($this->ride);
        $this->elements[] = $marker->draw();
    }
}
