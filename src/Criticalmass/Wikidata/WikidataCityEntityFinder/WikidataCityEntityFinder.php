<?php declare(strict_types=1);

namespace App\Criticalmass\Wikidata\WikidataCityEntityFinder;

use Wikidata\SearchResult;
use Wikidata\Wikidata;

class WikidataCityEntityFinder
{
    /** @var SearchResult $wikidata */
    protected $wikidata;

    public function __construct()
    {
        $this->wikidata = new Wikidata();
    }

    public function queryForIds(string $cityName, string $language = 'de', int $limit = 5): array
    {
        /** @var \Illuminate\Support\Collection $results */
        return $this->wikidata->search($cityName, $language, $limit)->toArray();
    }
}