<?php declare(strict_types=1);

namespace Tests\Controller\Api\CityApi;

use App\Entity\City;
use Tests\Controller\Api\AbstractApiControllerTest;

class CityApiTest extends AbstractApiControllerTest
{
    /**
     * @testdox Request Hamburg and check the result.
     */
    public function testCityHamburg(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/hamburg');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        /** @var City $actualCity */
        $actualCity = $this->deserializeEntity($client->getResponse()->getContent(), City::class);

        $this->assertEquals('Hamburg', $actualCity->getCity());
    }

    /**
     * @testdox Ask for a unknown city and retrieve 404.
     */
    public function testFirstRide(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/asdf');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
