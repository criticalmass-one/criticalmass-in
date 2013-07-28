<?php

namespace Caldera\CriticalmassBundle\Utility\MapBuilder;

use \Caldera\CriticalmassBundle\Utility\MapElement as MapElement;
use \Caldera\CriticalmassBundle\Entity as Entity;

abstract class BaseMapBuilder
{
	protected $positions = array();

	protected $mainPositions;

	protected $additionalPositions;

	protected $ride;

	public function __construct(Entity\Ride $ride, $positions)
	{
		$this->ride = $ride;
		$this->positions = $positions;
	}

	public abstract function getUserCounter();

	public abstract function getAverageSpeed();

	public abstract function getZoomFactor();

	public abstract function getMapCenterLatitude();

	public abstract function getMapCenterLongitude();

	public abstract function calculateMainPositions();

	public abstract function calculateAdditionalPositions();

	public function getMainPositions()
	{
		$resultArray = array();
		$counter = 0;

		foreach ($this->mainPositions as $position)
		{
			$circle = new MapElement\CircleMapElement($position, 100);

			$resultArray['position-'.$counter] = $circle->draw();
			++$counter;
		}

		return $resultArray;
	}

	public function getAdditionalPositions()
	{
		$resultArray = array();

		foreach ($this->additionalPositions as $position)
		{
			$circle = new MapElement\CircleMapElement($position, 10);

			$resultArray[$position->getId()] = $circle->draw();
		}

		return $resultArray;

	}

	public function draw()
	{
		return array(
			'mapcenter' => array(
				'latitude' => $this->getMapCenterLatitude(),
				'longitude' => $this->getMapCenterLongitude()
				),
				'zoom' => $this->getZoomFactor(),
				'mainpositions' => $this->getMainPositions(),
				'additionalpositions' => $this->getAdditionalPositions(),
				'usercounter' => $this->getUserCounter(),
				'averagespeed' => $this->getAverageSpeed()
			);
	}
}