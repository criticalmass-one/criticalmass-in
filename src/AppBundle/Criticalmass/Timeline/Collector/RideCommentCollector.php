<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Timeline\Collector;

use AppBundle\Entity\Post;
use AppBundle\Criticalmass\Timeline\Item\RideCommentItem;

class RideCommentCollector extends AbstractTimelineCollector
{
    protected $entityClass = Post::class;

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
    {
        /** @var Post $postEntity */
        foreach ($groupedEntities as $postEntity) {
            $item = new RideCommentItem();

            $item
                ->setUser($postEntity->getUser())
                ->setRideTitle($postEntity->getRide()->getFancyTitle())
                ->setRide($postEntity->getRide())
                ->setText($postEntity->getMessage())
                ->setDateTime($postEntity->getDateTime());

            $this->addItem($item);
        }

        return $this;
    }
}
