<?php declare(strict_types=1);

namespace Tests\Controller\Api\CityApi;

use App\Entity\City;
use Tests\Controller\Api\AbstractApiControllerTest;

class OrderByParameterTest extends AbstractApiControllerTest
{
    /**
     * @testdox Get 10 cities ordered by title ascending.
     */
    public function testCityListOrderByTitleAscending(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/city?orderBy=title&orderDirection=ASC');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualCityList = $this->deserializeEntityList($client->getResponse()->getContent(), City::class);

        $this->assertCount(10, $actualCityList);

        $minTitle = null;

        /**
         * @var City $actualCity
         */
        foreach ($actualCityList as $actualCity) {
            if ($minTitle) {
                $this->assertLessThanOrEqual($actualCity->getTitle(), $minTitle);
            }

            $minTitle = $actualCity->getTitle();
        }
    }

    /**
     * @testdox Get 10 cities ordered by title descending.
     */
    public function testCityListOrderByDateTimeDescending(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/city?orderBy=title&orderDirection=DESC');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualCityList = $this->deserializeEntityList($client->getResponse()->getContent(), City::class);

        $this->assertCount(10, $actualCityList);

        $maxTitle = null;

        /**
         * @var City $actualCity
         */
        foreach ($actualCityList as $actualCity) {
            if ($maxTitle) {
                $this->assertGreaterThanOrEqual($actualCity->getTitle(), $maxTitle);
            }

            $maxTitle = $actualCity->getTitle();
        }
    }

    /**
     * @testdox Providing invalid order direction will not break things.
     */
    public function testCityListOrderByTitleInvalidOrder(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/city?orderBy=title&orderDirection=FOO');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualCityList = $this->deserializeEntityList($client->getResponse()->getContent(), City::class);

        $this->assertCount(10, $actualCityList);
    }

    /**
     * @testdox Providing invalid fields will not break api.
     */
    public function testCityListOrderByInvalidOrder(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/city?orderBy=invalidField&orderDirection=DESC');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualCityList = $this->deserializeEntityList($client->getResponse()->getContent(), City::class);

        $this->assertCount(10, $actualCityList);
    }
}
