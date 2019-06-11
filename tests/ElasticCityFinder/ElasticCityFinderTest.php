<?php declare(strict_types=1);

namespace Tests\ElasticCityFinder;

use App\Criticalmass\ElasticCityFinder\ElasticCityFinder;
use App\Entity\City;
use Elastica\Query;
use FOS\ElasticaBundle\Finder\FinderInterface;
use PHPUnit\Framework\TestCase;

class ElasticCityFinderTest extends TestCase
{
    public function testCityWithoutLatLng(): void
    {
        $city = new City();
        $city->setId(42);

        $finder = $this->createMock(FinderInterface::class);

        $elasticCityFinder = new ElasticCityFinder($finder);

        $actualCityList = $elasticCityFinder->findNearCities($city);

        $this->assertEquals([], $actualCityList);
    }

    public function testResultForCityWihLatLng(): void
    {
        $city = new City();
        $city
            ->setId(42)
            ->setLatitude(57.5)
            ->setLongitude(10.5);

        $returnCity = new City();
        $returnCity
            ->setId(43)
            ->setLatitude(58.2)
            ->setLongitude(11.2);

        $finder = $this->createMock(FinderInterface::class);
        $finder
            ->expects($this->once())
            ->method('find')
            ->will($this->returnValue([$returnCity]));

        $elasticCityFinder = new ElasticCityFinder($finder);

        $actualCityList = $elasticCityFinder->findNearCities($city);

        $this->assertEquals([$returnCity], $actualCityList);
    }

    public function testQueryForCityWihLatLng(): void
    {
        $city = new City();
        $city
            ->setId(42)
            ->setLatitude(57.5)
            ->setLongitude(10.5);

        $finder = $this->createMock(FinderInterface::class);

        $elasticCityFinder = new ElasticCityFinder($finder);

        $query = $elasticCityFinder->createQuery($city);

        $expectedQuery = '{"query":{"bool":{"must":[{"geo_distance":{"distance":"50km","pin":{"lat":57.5,"lon":10.5}}},{"term":{"isEnabled":true}},{"bool":{"must_not":[{"term":{"id":42}}]}}]}},"size":15,"sort":{"_geo_distance":{"pin":[10.5,57.5],"order":"desc","unit":"km"}}}';

        $actualQuery = json_encode($query->toArray());

        $this->assertEquals($expectedQuery, $actualQuery);
    }
}