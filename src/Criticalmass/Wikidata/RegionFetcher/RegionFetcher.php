<?php declare(strict_types=1);

namespace App\Criticalmass\Wikidata\RegionFetcher;

use App\Entity\Region;
use Wikidata\SearchResult;
use Wikidata\SparqlClient;

class RegionFetcher implements RegionFetcherInterface
{
    const ADMINISTRATIVE_TERRITORIAL_PROPERTY = 'P1696';

    public function fetch(Region $region, string $language = 'de'): ?array
    {
        if (!$region->getWikidataEntityId()) {
            return null;
        }

        $client = new SparqlClient();

        $data = $client->execute('SELECT ?item ?itemLabel ?itemDescription WHERE {
  ?item wdt:P131 wd:Q183;
    wdt:P31 ?administrativeLevel.
  ?administrativeLevel wdt:P279 wd:Q10864048.
    SERVICE wikibase:label { bd:serviceParam wikibase:language "[AUTO_LANGUAGE],de". }
}');

        $collection = collect($data);

        $output = $collection->map(function($item) use ($language) {
            return SearchResultToRegionConverter::convert(new SearchResult($item, $language, 'sparql'));
        });

        return $output->toArray();
    }
}
