<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Timeline\Collector;

use AppBundle\Entity\City;
use AppBundle\Criticalmass\Timeline\Item\CityCreatedItem;

class CityCreatedCollector extends AbstractTimelineCollector
{
    protected $entityClass = City::class;

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
    {
        /** @var City $city */
        foreach ($groupedEntities as $city) {
            if ($city->getSlugs()) {
                $item = new CityCreatedItem();

                $item
                    ->setUser($city->getUser())
                    ->setUsername($city->getUser()->getUsername())
                    ->setCityName($city->getCity())
                    ->setCity($city)
                    ->setDateTime($city->getCreatedAt());

                $this->addItem($item);
            }
        }

        return $this;
    }
}
