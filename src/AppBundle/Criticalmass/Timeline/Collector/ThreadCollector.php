<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Timeline\Collector;

use AppBundle\Entity\Thread;
use AppBundle\Criticalmass\Timeline\Item\ThreadItem;

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
                ->setUsername($threadEntity->getFirstPost()->getUser()->getUsername())
                ->setThread($threadEntity)
                ->setTitle($threadEntity->getTitle())
                ->setText($threadEntity->getFirstPost()->getMessage())
                ->setDateTime($threadEntity->getFirstPost()->getDateTime());

            $this->addItem($item);
        }

        return $this;
    }
}
