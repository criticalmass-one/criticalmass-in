<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Collector;

use Caldera\Bundle\CalderaBundle\Entity\Post;
use Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item\RideCommentItem;

class RideCommentCollector extends AbstractTimelineCollector
{
    protected function fetchEntities()
    {
        return $this->doctrine->getRepository('CalderaBundle:Post')->findForTimelineRideCommentCollector($this->startDateTime, $this->endDateTime);
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
            $item = new RideCommentItem();

            $item->setUsername($postEntity->getUser()->getUsername());
            $item->setRideTitle($postEntity->getRide()->getFancyTitle());
            $item->setRide($postEntity->getRide());
            $item->setText($postEntity->getMessage());
            $item->setDateTime($postEntity->getDateTime());

            $this->addItem($item);
        }
    }
}