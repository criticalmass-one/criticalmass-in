<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline;

use App\Criticalmass\Feature\FeatureManager\FeatureManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Templating\EngineInterface;

class CachedTimeline extends Timeline
{
    protected $ttl;

    public function __construct(RegistryInterface $doctrine, EngineInterface $templating, FeatureManagerInterface $featureManager, int $cachedTimelineTtl = 300)
    {
        $this->doctrine = $doctrine;
        $this->templating = $templating;
        $this->ttl = $cachedTimelineTtl;

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
