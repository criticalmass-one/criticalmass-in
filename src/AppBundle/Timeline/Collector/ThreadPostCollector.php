<?php

namespace AppBundle\Timeline\Collector;

use AppBundle\Entity\Post;
use AppBundle\Timeline\Item\ThreadPostItem;

class ThreadPostCollector extends AbstractTimelineCollector
{
    private $entityClass = Post::class;

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
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

        return $this;
    }
}