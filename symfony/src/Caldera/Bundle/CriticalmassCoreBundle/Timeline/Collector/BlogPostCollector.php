<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Collector;

use Caldera\Bundle\CalderaBundle\Entity\Post;
use Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item\BlogPostItem;

class BlogPostCollector extends AbstractTimelineCollector
{
    protected function fetchEntities()
    {
        return $this->doctrine->getRepository('CalderaBundle:Post')->findForTimelineBlogPostCollector($this->startDateTime, $this->endDateTime);
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
            $item = new BlogPostItem();

            $item->setUsername($postEntity->getUser()->getUsername());
            $item->setTitle($postEntity->getTitle());
            $item->setPost($postEntity);
            $item->setText($postEntity->getMessage());
            $item->setDateTime($postEntity->getDateTime());

            $this->addItem($item);
        }
    }
}