<?php

namespace Criticalmass\Bundle\AppBundle\Timeline\Collector;

use Criticalmass\Bundle\AppBundle\Entity\Post;
use Criticalmass\Bundle\AppBundle\Timeline\Item\ThreadPostItem;

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
