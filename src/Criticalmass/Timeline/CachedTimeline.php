<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline;

use Doctrine\Persistence\ManagerRegistry;
use Flagception\Manager\FeatureManagerInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Templating\EngineInterface;

class CachedTimeline extends Timeline
{
    protected int $ttl;

    protected string $redisUrl;

    public function __construct(ManagerRegistry $doctrine, EngineInterface $templating, FeatureManagerInterface $featureManager, string $redisUrl, int $cachedTimelineTtl = 300)
    {
        $this->doctrine = $doctrine;
        $this->templating = $templating;
        $this->ttl = $cachedTimelineTtl;
        $this->redisUrl = $redisUrl;

        parent::__construct($doctrine, $templating, $featureManager);
    }

    public function execute(): TimelineInterface
    {
        $cacheKey = 'criticalmass-timeline-content';

        if ($this->startDateTime) {
            $cacheKey .= '-start-' . $this->startDateTime->format('Y-m-d');
        }

        if ($this->endDateTime) {
            $cacheKey .= '-end-' . $this->endDateTime->format('Y-m-d');
        }

        $redisConnection = RedisAdapter::createConnection($this->redisUrl);

        $cache = new RedisAdapter(
            $redisConnection,
            $namespace = '',
            $defaultLifetime = 0
        );

        $timeline = $cache->getItem($cacheKey);

        if (!$timeline->isHit()) {
            $this->process();

            $timeline
                ->set($this->getTimelineContentList())
                ->expiresAfter($this->ttl);

            $cache->save($timeline);
        } else {
            $this->content = $timeline->get();
        }

        return $this;
    }
}
