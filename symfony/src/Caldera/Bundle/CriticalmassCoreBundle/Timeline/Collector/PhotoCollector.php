<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Collector;

class PhotoCollector extends AbstractTimelineCollector
{
    public function execute()
    {
        $this->doctrine->getRepository('CalderaCriticalmassModelBundle:Photo')->findAll();
    }
}