<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Collector;

abstract class AbstractTimelineCollector
{
    protected $doctrine;

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    abstract public function execute();
}