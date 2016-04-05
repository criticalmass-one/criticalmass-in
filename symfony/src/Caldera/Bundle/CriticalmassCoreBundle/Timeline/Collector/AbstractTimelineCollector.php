<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Collector;

abstract class AbstractTimelineCollector
{
    protected $doctrine;

    protected $items = [];

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    abstract public function execute();

    public function getItems()
    {
        return $this->items;
    }
}