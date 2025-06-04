<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Collector;

use App\Entity\City;
use App\Criticalmass\Timeline\Item\CityCreatedItem;

class CityCreatedCollector extends AbstractTimelineCollector
{
    protected string $entityClass = City::class;

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
    {
        /** @var City $city */
        foreach ($groupedEntities as $city) {
            if ($city->getSlugs()) {
                $item = new CityCreatedItem();

                $item
                    ->setUser($city->getUser())
                    ->setCityName($city->getCity())
                    ->setCity($city)
                    ->setDateTime($city->getCreatedAt());

                $this->addItem($item);
            }
        }

        return $this;
    }
}
