<?php

namespace AppBundle\Criticalmass\Timeline;

use AppBundle\Feature\FeatureManager;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Templating\EngineInterface;

class CachedTimeline extends Timeline
{
    protected $ttl;

    public function __construct(RegistryInterface $doctrine, EngineInterface $templating, FeatureManager $featureManager, int $ttl = 300)
    {

        $this->doctrine = $doctrine;
        $this->templating = $templating;
        $this->ttl = $ttl;

        parent::__construct($doctrine, $templating, $featureManager);
    }

    public function execute(): Timeline
    {
        $cacheKey = 'criticalmass-timeline-content';

        if ($this->startDateTime) {
            $cacheKey .= '-start-' . $this->startDateTime->format('Y-m-d');
        }

        if ($this->endDateTime) {
            $cacheKey .= '-end-' . $this->endDateTime->format('Y-m-d');
        }

        $redisConnection = RedisAdapter::createConnection('redis://localhost');

        $cache = new RedisAdapter(
            $redisConnection,
            $namespace = '',
            $defaultLifetime = 0
        );

        $timeline = $cache->getItem($cacheKey);

        if (!$timeline->isHit()) {
            $this->process();

            $timeline
                ->set($this->content)
                ->expiresAfter($this->ttl);

            $cache->save($timeline);
        } else {
            $this->content = $timeline->get();
        }

        return $this;
    }
}
