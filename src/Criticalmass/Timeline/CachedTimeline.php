<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline;

use Doctrine\Persistence\ManagerRegistry;
use Flagception\Manager\FeatureManagerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use Twig\Environment;

class CachedTimeline extends Timeline
{
    protected int $ttl;

    public function __construct(ManagerRegistry $doctrine, Environment $twigEnvironment, FeatureManagerInterface $featureManager, int $cachedTimelineTtl = 300)
    {
        $this->doctrine = $doctrine;
        $this->twigEnvironment = $twigEnvironment;
        $this->ttl = $cachedTimelineTtl;

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

        $cache = new FilesystemAdapter(
            'criticalmass-timeline',
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
