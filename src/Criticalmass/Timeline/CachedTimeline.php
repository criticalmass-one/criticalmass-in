<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline;

use Doctrine\Persistence\ManagerRegistry;
use Flagception\Manager\FeatureManagerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class CachedTimeline extends Timeline
{
    private const int TTL = 300;

    public function execute(): TimelineInterface
    {
        $cacheKey = 'criticalmass-timeline-json-v1';

        if ($this->startDateTime) {
            $cacheKey .= '-start-' . $this->startDateTime->format('Y-m-d');
        }

        if ($this->endDateTime) {
            $cacheKey .= '-end-' . $this->endDateTime->format('Y-m-d');
        }

        $cache = new FilesystemAdapter(
            'criticalmass-timeline-json',
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
