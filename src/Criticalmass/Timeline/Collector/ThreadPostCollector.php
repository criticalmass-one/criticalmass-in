<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Collector;

use App\Entity\Post;
use App\Criticalmass\Timeline\Item\ThreadPostItem;

class ThreadPostCollector extends AbstractTimelineCollector
{
    protected string $entityClass = Post::class;

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
    {
        /** @var Post $threadEntity */
        foreach ($groupedEntities as $postEntity) {
            $item = new ThreadPostItem();

            $item
                ->setPostId($postEntity->getId())
                ->setUser($postEntity->getUser())
                ->setThreadTitle($postEntity->getThread()->getTitle())
                ->setThread($postEntity->getThread())
                ->setText($postEntity->getMessage())
                ->setDateTime($postEntity->getDateTime());

            $this->addItem($item);
        }

        return $this;
    }
}
