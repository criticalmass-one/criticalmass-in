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
        $cacheKey = 'timeline-content';

        if ($this->startDateTime) {
            $cacheKey .= '-start-'.$this->startDateTime->format('Y-m-d-H-i-s');
        }

        if ($this->endDateTime) {
            $cacheKey .= '-end-'.$this->endDateTime->format('Y-m-d-H-i-s');
        }

        $cachedContent = $this->memcache->get($cacheKey);

        if ($cachedContent) {
            $this->content = $cachedContent;
        } else {
            $this->process();

            $this->memcache->set('timeline-content', $this->content, 0, $this->ttl);
        }

        return $this;
    }
}

