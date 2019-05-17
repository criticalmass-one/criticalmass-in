<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Collector;

use App\Entity\City;
use App\Criticalmass\Timeline\Item\CityEditItem;

class CityEditCollector extends AbstractTimelineCollector
{
    protected $entityClass = City::class;

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
    {
        /** @var City $city */
        foreach ($groupedEntities as $city) {
            if ($city->getSlugs()) {
                $item = new CityEditItem();

                $item
                    ->setUser($city->getUser())
                    ->setCityName($city->getCity())
                    ->setCity($city)
                    ->setDateTime($city->getUpdatedAt());

                $this->addItem($item);
            }
        }

        return $this;
    }
}
