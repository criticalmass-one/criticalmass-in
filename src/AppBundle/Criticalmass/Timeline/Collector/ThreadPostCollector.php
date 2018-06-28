<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Timeline\Collector;

use AppBundle\Entity\Post;
use AppBundle\Criticalmass\Timeline\Item\ThreadPostItem;

class ThreadPostCollector extends AbstractTimelineCollector
{
    protected $entityClass = Post::class;

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
    {
        /** @var Post $threadEntity */
        foreach ($groupedEntities as $postEntity) {
            $item = new ThreadPostItem();

            $item
                ->setUser($postEntity->getUser())
                ->setUsername($postEntity->getUser()->getUsername())
                ->setThreadTitle($postEntity->getThread()->getTitle())
                ->setThread($postEntity->getThread())
                ->setText($postEntity->getMessage())
                ->setDateTime($postEntity->getDateTime());

            $this->addItem($item);
        }

        return $this;
    }
}
