<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Collector;

use App\Criticalmass\Timeline\Item\RideCommentItem;
use App\Entity\Post;

class RideCommentCollector extends AbstractTimelineCollector
{
    protected string $entityClass = Post::class;

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
                ->setDateTime($postEntity->getDateTime())
                ->setRideEnabled($postEntity->getRide()->isEnabled());

            $this->addItem($item);
        }

        return $this;
    }
}
