<?php declare(strict_types=1);

namespace Tests\Controller\Api\CityApi;

use App\Entity\City;
use Tests\Controller\Api\AbstractApiControllerTest;

class StartValueParameterTest extends AbstractApiControllerTest
{
    /**
     * @testdox Providing a startValue without any orderBy is ignored.
     */
    public function testCityListWithStartValueParameterOnly(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/city?startValue=hamburg');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @testdox Requesting rides later than Hamburg.
     */
    public function testCityListWithStartValueAndOrderByParameterAscending(): void
    {
        $cityName = 'Hamburg';

        $client = static::createClient();

        $client->request('GET', sprintf('/api/city?orderBy=city&orderDirection=ASC&startValue=%s', $cityName));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualCityList = $this->deserializeEntityList($client->getResponse()->getContent(), City::class);

        $this->assertCount(10, $actualCityList);

        /** @var City $actualCity */
        foreach ($actualCityList as $actualCity) {
            $this->assertGreaterThanOrEqual($cityName, $actualCity->getCity());

            $cityName = $actualCity->getCity();
        }
    }

    /**
     * @testdox Requesting city lower than Hamburg. There are only 6 cities in our fixtures lower than Hamburg.
     */
    public function testCityListWithStartValueAndOrderByParameterDescending(): void
    {
        $cityName = 'Hamburg';

        $client = static::createClient();

        $client->request('GET', sprintf('/api/city?orderBy=city&orderDirection=DESC&startValue=%s', $cityName));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $actualCityList = $this->deserializeEntityList($client->getResponse()->getContent(), City::class);

        $this->assertCount(6, $actualCityList);

        /** @var City $actualCity */
        foreach ($actualCityList as $actualCity) {
            $this->assertLessThanOrEqual($cityName, $actualCity->getDateTime());

            $cityName = $actualCity->getCity();
        }
    }
}
