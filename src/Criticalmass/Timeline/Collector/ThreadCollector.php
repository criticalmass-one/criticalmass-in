<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Collector;

use App\Entity\Thread;
use App\Criticalmass\Timeline\Item\ThreadItem;

class ThreadCollector extends AbstractTimelineCollector
{
    protected $entityClass = Thread::class;

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
    {
        /** @var Thread $threadEntity */
        foreach ($groupedEntities as $threadEntity) {
            $item = new ThreadItem();

            $item
                ->setUser($threadEntity->getFirstPost()->getUser())
                ->setThread($threadEntity)
                ->setTitle($threadEntity->getTitle())
                ->setText($threadEntity->getFirstPost()->getMessage())
                ->setDateTime($threadEntity->getFirstPost()->getDateTime());

            $this->addItem($item);
        }

        return $this;
    }
}
