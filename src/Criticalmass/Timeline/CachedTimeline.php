<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline;

use Doctrine\Persistence\ManagerRegistry;
use Flagception\Manager\FeatureManagerInterface;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use Twig\Environment;

class CachedTimeline extends Timeline
{
    protected int $ttl;

    protected string $redisUrl;

    public function __construct(ManagerRegistry $doctrine, Environment $twigEnvironment, FeatureManagerInterface $featureManager, string $redisUrl, int $cachedTimelineTtl = 300)
    {
        $this->doctrine = $doctrine;
        $this->twigEnvironment = $twigEnvironment;
        $this->ttl = $cachedTimelineTtl;
        $this->redisUrl = $redisUrl;

        parent::__construct($doctrine, $twigEnvironment, $featureManager);
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
            'criticalmass',
            $this->ttl
        );

        $this->contentList = $cache->get($cacheKey, function (ItemInterface $item) {
            $item->expiresAfter($this->ttl);

            $this->process();

            return $this->getTimelineContentList();
        });

        return $this;
    }
}
