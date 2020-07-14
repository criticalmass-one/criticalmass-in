<?php declare(strict_types=1);

namespace App\Criticalmass\Wikidata\RegionFetcher;

use App\Entity\Region;
use Malenki\Slug;
use Wikidata\SearchResult;

class SearchResultToRegionConverter
{
    private function __construct()
    {

    }

    public static function convert(SearchResult $searchResult): Region
    {
        $region = new Region();
        $region
            ->setWikidataEntityId($searchResult->id)
            ->setName($searchResult->label)
            ->setSlug(self::generateslug($searchResult->label));

        return $region;
    }

    public static function generateslug(string $regionName): string
    {
        return (string) (new Slug($regionName))->noHistory();
    }
}