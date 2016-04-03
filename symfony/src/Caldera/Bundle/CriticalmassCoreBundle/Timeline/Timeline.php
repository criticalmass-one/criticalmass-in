<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline;

use Caldera\Bundle\CriticalmassCoreBundle\Timeline\Collector\AbstractTimelineCollector;

class Timeline
{
    protected $collectorList = [];
    protected $items = [];

    public function __construct()
    {
    }

    public function addCollector(AbstractTimelineCollector $collector)
    {
        array_push($this->collectorList, $collector);
    }

    public function executeCollectors()
    {
        /**
         * @var AbstractTimelineCollector $collector
         */
        foreach ($this->collectorList as $collector) {
            $collector->execute();


        }
    }
}

