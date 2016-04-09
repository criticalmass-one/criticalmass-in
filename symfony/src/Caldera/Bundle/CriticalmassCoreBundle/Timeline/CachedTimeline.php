<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline;

use Caldera\Bundle\CriticalmassCoreBundle\Timeline\Collector\AbstractTimelineCollector;
use Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item\ItemInterface;

class CachedTimeline extends Timeline
{
    protected $memcache;
    protected $ttl;

    public function __construct($doctrine, $templating, $memcache, $ttl)
    {
        $this->doctrine = $doctrine;
        $this->templating = $templating;
        $this->memcache = $memcache;
        $this->ttl = $ttl;
    }

    public function execute()
    {
        $cachedContent = $this->memcache->get('timeline-content');

        if ($cachedContent) {
            echo "GET FROM CACHE";
            $this->content = $cachedContent;
        } else {
            echo "GENERATE";
            $this->process();

            $this->memcache->set('timeline-content', $this->content, 0, $this->ttl);
        }

        return $this;
    }
}

