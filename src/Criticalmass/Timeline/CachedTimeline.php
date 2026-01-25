<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline;

use Doctrine\Persistence\ManagerRegistry;
use Flagception\Manager\FeatureManagerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use Twig\Environment;

class CachedTimeline extends Timeline
{
    private const int TTL = 300;

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
            self::TTL
        );

        $this->contentList = $cache->get($cacheKey, function (ItemInterface $item) {
            $item->expiresAfter(self::TTL);

            $this->process();

            return $this->getTimelineContentList();
        });

        return $this;
    }
}
