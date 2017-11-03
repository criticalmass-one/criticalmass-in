<?php

namespace Criticalmass\Bundle\AppBundle\Timeline\Collector;

use Criticalmass\Bundle\AppBundle\Entity\Post;
use Criticalmass\Bundle\AppBundle\Timeline\Item\PhotoCommentItem;

class PhotoCommentCollector extends AbstractTimelineCollector
{
    protected $entityClass = Post::class;

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
    {
        /**
         * @var Post $threadEntity
         */
        foreach ($groupedEntities as $postEntity) {
            $item = new PhotoCommentItem();

            $item->setUsername($postEntity->getUser()->getUsername());
            $item->setRideTitle($postEntity->getPhoto()->getRide()->getFancyTitle());
            $item->setPhoto($postEntity->getPhoto());
            $item->setText($postEntity->getMessage());
            $item->setDateTime($postEntity->getDateTime());

            $this->addItem($item);
        }

        return $this;
    }
}
