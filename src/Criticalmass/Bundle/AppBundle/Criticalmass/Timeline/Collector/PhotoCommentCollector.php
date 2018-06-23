<?php

namespace Criticalmass\Bundle\AppBundle\Criticalmass\Timeline\Collector;

use Criticalmass\Bundle\AppBundle\Entity\Post;
use Criticalmass\Bundle\AppBundle\Criticalmass\Timeline\Item\PhotoCommentItem;

class PhotoCommentCollector extends AbstractTimelineCollector
{
    protected $entityClass = Post::class;

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
    {
        /** @var Post $postEntity */
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

    public function getRequiredFeatures(): array
    {
        return ['photos'];
    }
}
