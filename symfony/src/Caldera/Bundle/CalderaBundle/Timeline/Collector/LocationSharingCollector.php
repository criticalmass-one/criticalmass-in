<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Collector;

use Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item\LocationSharingItem;

class LocationSharingCollector extends AbstractTimelineCollector
{
    protected function fetchEntities()
    {
        $entities = [];

        $glympseTickets = $this->doctrine->getRepository('CalderaBundle:Ticket')->findForTimelineLocationSharingCollector($this->startDateTime, $this->endDateTime);
        $criticalmapsUsers = $this->doctrine->getRepository('CalderaBundle:CriticalmapsUser')->findForTimelineLocationSharingCollector($this->startDateTime, $this->endDateTime);

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
            $latLngList = [];

            foreach ($dayGroup as $entity) {
                $city = $entity->getCity();
                $cityId = $city->getId();

                if (!array_key_exists($cityId, $cityList)) {
                    $cityList[$cityId] = $city;

                    array_push($latLngList, [$city->getLatitude(), $city->getLongitude()]);
                }

                ++$sharingCounter;
            }

            $polyline = \Polyline::Encode($latLngList);

            $item = new LocationSharingItem();
            $item->setSharingCounter($sharingCounter);
            $item->setCityList($cityList);
            $item->setPolyline($polyline);

            $lastEntity = array_pop($dayGroup);

            $item->setDateTime($lastEntity->getCreationDateTime());

            $this->addItem($item);
        }
    }
}