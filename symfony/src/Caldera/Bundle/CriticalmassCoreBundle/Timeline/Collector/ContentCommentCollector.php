<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Collector;

use Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item\ContentCommentItem;

class ContentCommentCollector extends AbstractTimelineCollector
{
    protected function fetchEntities()
    {
        return $this->doctrine->getRepository('CalderaCriticalmassModelBundle:Post')->findForTimelineContentCommentCollector($this->startDateTime, $this->endDateTime);
    }

    protected function groupEntities(array $entities)
    {
        return $entities;
    }

    protected function convertGroupedEntities(array $groupedEntities)
    {
        /**
         * @var Post $postEntity
         */
        foreach ($groupedEntities as $postEntity) {
            $item = new ContentCommentItem();

            $item->setUsername($postEntity->getUser()->getUsername());
            $item->setContentTitle($postEntity->getContent()->getTitle());
            $item->setContent($postEntity->getContent());
            $item->setText($postEntity->getMessage());
            $item->setDateTime($postEntity->getDateTime());

            $this->addItem($item);
        }
    }
}