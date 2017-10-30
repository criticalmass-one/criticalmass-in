<?php

namespace AppBundle\Timeline\Collector;

use AppBundle\Entity\City;
use AppBundle\Timeline\Item\CityEditItem;

class CityEditCollector extends AbstractTimelineCollector
{
    protected function fetchEntities(): array
    {
        return $this->doctrine->getRepository('AppBundle:City')->findForTimelineCityEditCollector($this->startDateTime, $this->endDateTime);
    }

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
    {
        /**
         * @var City $city
         */
        foreach ($groupedEntities as $city) {
            if ($city->getSlugs()) {
                $item = new CityEditItem();

                $item->setUsername($city->getUser()->getUsername());
                $item->setCityName($city->getCity());
                $item->setCity($city);
                $item->setDateTime($city->getUpdatedAt());
                $this->addItem($item);
            }
        }

        return $this;
    }
}