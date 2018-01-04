<?php

namespace Criticalmass\Component\Timeline\Collector;

use Criticalmass\Component\Entity\Post;
use Criticalmass\Component\Timeline\Item\ThreadPostItem;

class ThreadPostCollector extends AbstractTimelineCollector
{
    protected $entityClass = Post::class;

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
