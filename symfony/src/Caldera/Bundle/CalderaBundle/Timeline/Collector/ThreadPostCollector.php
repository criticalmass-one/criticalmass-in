<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Collector;

use Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item\ThreadPostItem;

class ThreadPostCollector extends AbstractTimelineCollector
{
    protected function fetchEntities()
    {
        return $this->doctrine->getRepository('CalderaBundle:Post')->findForTimelineThreadPostCollector($this->startDateTime, $this->endDateTime);
    }

    protected function groupEntities(array $entities)
    {
        return $entities;
    }

    protected function convertGroupedEntities(array $groupedEntities)
    {
        /**
         * @var Post $threadEntity
         */
        foreach ($groupedEntities as $postEntity) {
            $item = new ThreadPostItem();

            $item->setUsername($postEntity->getUser()->getUsername());
            $item->setThreadTitle($postEntity->getThread()->getTitle());
            $item->setThread($postEntity->getThread());
            $item->setText($postEntity->getMessage());
            $item->setDateTime($postEntity->getDateTime());

            $this->addItem($item);
        }
    }
}