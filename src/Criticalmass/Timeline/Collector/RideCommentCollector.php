<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Collector;

use App\Entity\Post;
use App\Criticalmass\Timeline\Item\RideCommentItem;

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
                ->setRideTitle($postEntity->getRide()->getTitle())
                ->setRide($postEntity->getRide())
                ->setPost($postEntity)
                ->setText($postEntity->getMessage())
                ->setDateTime($postEntity->getDateTime());

            $this->addItem($item);
        }

        return $this;
    }
}
