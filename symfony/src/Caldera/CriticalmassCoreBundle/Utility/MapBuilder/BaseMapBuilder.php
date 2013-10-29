<?php

namespace Caldera\CriticalmassCoreBundle\Utility\MapBuilder;

use \Caldera\CriticalmassCoreBundle\Utility\MapElement as MapElement;
use \Caldera\CriticalmassCoreBundle\Entity as Entity;
use \Caldera\CriticalmassCoreBundle\Utility\PositionArray as PositionArray;
use \Caldera\CriticalmassCoreBundle\Utility\MapBuilderModule as MapBuilderModule;

abstract class BaseMapBuilder
{
	public $positionArray;

    public $doctrine;

    public $elements = array();

    public $ride;

    public $modules = array();

    public $response;

	public function __construct(Entity\Ride $ride, \Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
	{
		$this->ride = $ride;

		$this->doctrine = $doctrine;

        $this->positionArray = new PositionArray();
	}

    public function registerModule($moduleName)
    {
        $moduleName = "\\Caldera\\CriticalmassCoreBundle\\Utility\\MapBuilderModule\\".$moduleName;

        $this->modules[$moduleName] = new $moduleName($this);
    }

    public function execute()
    {
        foreach ($this->modules as $module)
        {
            $module->execute();
        }
    }
	public function draw()
	{
        $this->response['elements'] = $this->elements;

		return $this->response;
	}
}
