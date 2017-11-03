<?php

namespace Criticalmass\Bundle\AppBundle\Timeline\Collector;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Timeline\Item\CityEditItem;

class CityEditCollector extends AbstractTimelineCollector
{
    protected $entityClass = City::class;

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
