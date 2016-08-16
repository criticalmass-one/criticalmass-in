<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Collector;

use Caldera\Bundle\CalderaBundle\Entity\BlogPost;
use Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item\BlogPostItem;

class BlogPostCollector extends AbstractTimelineCollector
{
    protected function fetchEntities()
    {
        return $this->doctrine->getRepository('CalderaBundle:BlogPost')->findForTimelineBlogPostCollector($this->startDateTime, $this->endDateTime);
    }

    protected function groupEntities(array $entities)
    {
        return $entities;
    }

    protected function convertGroupedEntities(array $groupedEntities)
    {
        /**
         * @var BlogPost $postEntity
         */
        foreach ($groupedEntities as $postEntity) {
            $item = new BlogPostItem();

            $item->setUsername($postEntity->getUser()->getUsername());
            $item->setTitle($postEntity->getTitle());
            $item->setBlogPost($postEntity);
            $item->setText($postEntity->getText());
            $item->setDateTime($postEntity->getDateTime());

            $this->addItem($item);
        }
    }
}