<?php declare(strict_types=1);

namespace App\Criticalmass\Wikidata\CityPopulationFetcher;

use App\Criticalmass\CityPopulationFetcher\Exception\CityNotFoundException;
use App\Criticalmass\CityPopulationFetcher\Exception\ValueNotFoundException;
use App\Criticalmass\CityPopulationFetcher\Exception\ValueNotParseableException;
use Curl\Curl;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Wikidata\SearchResult;
use Wikidata\Wikidata;

class WikidataCityPopulationFetcher implements CityPopulationFetcherInterface
{
    public function fetch(string $cityName): ?int
    {
        $wikidata = new Wikidata();

        /** @var \Illuminate\Support\Collection $results */
        $results = $wikidata->search($cityName, 'de', 1);

        $city = $wikidata->get($results->first()->id);

        $population = (int) $city->properties['P1082']->value;

        return $population;
    }
}