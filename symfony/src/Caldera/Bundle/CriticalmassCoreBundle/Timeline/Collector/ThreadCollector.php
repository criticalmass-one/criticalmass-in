<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Collector;

use Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item\ThreadItem;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Track;

class ThreadCollector extends AbstractTimelineCollector
{
    public function execute()
    {
        $threadEntities = $this->fetchEntities();
        $sortedEntities = $this->groupEntities($threadEntities);
        $this->convertGroupedEntities($sortedEntities);
    }

    protected function fetchEntities()
    {
        return $this->doctrine->getRepository('CalderaCriticalmassModelBundle:Thread')->findForTimelineRideThreadCollector();
    }

    protected function groupEntities(array $threadEntities)
    {
        return $threadEntities;
    }

    protected function convertGroupedEntities(array $groupedEntities)
    {
        /**
         * @var Thread $threadEntity
         */
        foreach ($groupedEntities as $threadEntity) {
            $item = new ThreadItem();

            $item->setUser($threadEntity->getFirstPost()->getUser());
            $item->setThread($threadEntity);
            $item->setTitle($threadEntity->getTitle());
            $item->setText($threadEntity->getFirstPost()->getMessage());
            $item->setDateTime($threadEntity->getFirstPost()->getDateTime());

            $this->addItem($item);
        }
    }
}