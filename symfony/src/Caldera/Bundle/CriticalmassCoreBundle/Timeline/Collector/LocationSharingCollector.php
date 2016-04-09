<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Collector;

use Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item\LocationSharingItem;

class LocationSharingCollector extends AbstractTimelineCollector
{
    protected function fetchEntities()
    {
        $entities = [];

        $glympseTickets = $this->doctrine->getRepository('CalderaCriticalmassModelBundle:Ticket')->findForTimelineLocationSharingCollector();
        $criticalmapsUsers = $this->doctrine->getRepository('CalderaCriticalmassModelBundle:CriticalmapsUser')->findForTimelineLocationSharingCollector();

        $entities = array_merge($glympseTickets, $criticalmapsUsers);

        return $entities;
    }

    protected function groupEntities(array $entities)
    {
        $groupedEntities = [];

        foreach ($entities as $entity) {
            $dateTime = $entity->getCreationDateTime();

            $groupedEntities[$dateTime->format('Y-m-d')][] = $entity;
        }

        return $groupedEntities;
    }

    protected function convertGroupedEntities(array $groupedEntities)
    {
        foreach ($groupedEntities as $dayGroup) {
            $sharingCounter = 0;
            $cityList = [];

            foreach ($dayGroup as $entity) {
                $cityId = $entity->getCity()->getId();

                $cityList[$cityId] = $entity->getCity();

                ++$sharingCounter;
            }

            $item = new LocationSharingItem();
            $item->setSharingCounter($sharingCounter);
            $item->setCityList($cityList);

            $lastEntity = array_pop($dayGroup);

            $item->setDateTime($lastEntity->getCreationDateTime());

            $this->addItem($item);
        }
    }
}