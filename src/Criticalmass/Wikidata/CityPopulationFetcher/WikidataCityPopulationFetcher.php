<?php declare(strict_types=1);

namespace App\Criticalmass\Wikidata\CityPopulationFetcher;

use App\Entity\City;
use Wikidata\Wikidata;

class WikidataCityPopulationFetcher implements CityPopulationFetcherInterface
{
    const POPULATION_PROPERTY = 'P1082';

    public function fetch(City $city): ?int
    {
        if (!$city->getWikidataEntityId()) {
            return null;
        }

        $wikidata = new Wikidata();

        $cityData = $wikidata->get($city->getWikidataEntityId());

        return (int) $cityData->properties[self::POPULATION_PROPERTY]->value;
    }
}