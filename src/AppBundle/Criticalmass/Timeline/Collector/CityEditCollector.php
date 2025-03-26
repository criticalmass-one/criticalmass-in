<?php

namespace AppBundle\Criticalmass\Timeline\Collector;

use AppBundle\Entity\City;
use AppBundle\Criticalmass\Timeline\Item\CityEditItem;

class CityEditCollector extends AbstractTimelineCollector
{
    protected $entityClass = City::class;

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
    {
        /** @var City $city */
        foreach ($groupedEntities as $city) {
            if ($city->getSlugs()) {
                $item = new CityEditItem();

                $item->setUser($city->getUser());
                $item->setCityName($city->getCity());
                $item->setCity($city);
                $item->setDateTime($city->getUpdatedAt());
                $this->addItem($item);
            }
        }

        return $this;
    }
}
