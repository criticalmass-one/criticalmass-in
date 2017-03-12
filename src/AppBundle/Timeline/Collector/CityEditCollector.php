<?php

namespace AppBundle\Timeline\Collector;

use AppBundle\Entity\City;
use AppBundle\Timeline\Item\CityEditItem;

class CityEditCollector extends AbstractTimelineCollector
{
    protected function fetchEntities()
    {
        return $this->doctrine->getRepository('CalderaBundle:City')->findForTimelineCityEditCollector($this->startDateTime, $this->endDateTime);
    }

    protected function groupEntities(array $entities)
    {
        return $entities;
    }

    protected function convertGroupedEntities(array $groupedEntities)
    {
        /**
         * @var City $city
         */
        foreach ($groupedEntities as $city) {
            if ($city->getSlugs()) {
                $item = new CityEditItem();

                $item->setUsername($city->getArchiveUser()->getUsername());
                $item->setCityName($city->getCity());
                $item->setCity($city);
                $item->setDateTime($city->getArchiveDateTime());
                $item->setArchiveMessage($city->getArchiveMessage());
                $this->addItem($item);
            }
        }
    }
}