<?php declare(strict_types=1);

namespace Tests\Wikidata\RegionFetcher;

use App\Criticalmass\Wikidata\RegionFetcher\SearchResultToRegionConverter;
use App\Entity\Region;
use PHPUnit\Framework\TestCase;
use Wikidata\SearchResult;

class SearchResultToRegionConverterTest extends TestCase
{
    public function testFoo(): void
    {
        $expectedRegion = new Region();
        $expectedRegion
            ->setName('Schleswig-Holstein')
            ->setSlug('schleswig-holstein')
            ->setWikidataEntityId('QABC123');

        $data = [
            'id' => 'QABC123',
            'label' => 'Schleswig-Holstein',
            'description' => 'Foo bar baz',
        ];

        $searchResult = new SearchResult($data);

        $actualRegion = SearchResultToRegionConverter::convert($searchResult);

        $this->assertEquals($expectedRegion, $actualRegion);
    }
}