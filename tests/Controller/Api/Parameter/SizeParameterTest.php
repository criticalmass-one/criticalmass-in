<?php declare(strict_types=1);

namespace Tests\Controller\Api\Parameter;

use App\Entity\City;
use App\Entity\Photo;
use App\Entity\Ride;
use Tests\Controller\Api\AbstractApiControllerTest;

class SizeParameterTest extends AbstractApiControllerTest
{
    /**
     * @dataProvider apiClassProvider
     * @testdox Calling api without size parameter delivers 10 results.
     */
    public function testResultListWithBoundingSizeParameter(string $fqcn): void
    {
        $client = static::createClient();

        $client->request('GET', sprintf('%s', $this->getApiEndpointForFqcn($fqcn)));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($client->getResponse()->getContent(), $fqcn);

        $this->assertCount(10, $resultList);
    }

    /**
     * @dataProvider apiClassProvider
     * @testdox Request 5 results.
     */
    public function testResultListWith5Results(string $fqcn): void
    {
        $client = static::createClient();

        $client->request('GET', sprintf('%s?size=5', $this->getApiEndpointForFqcn($fqcn)));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($client->getResponse()->getContent(), $fqcn);

        $this->assertCount(5, $resultList);
    }

    /**
     * @dataProvider apiClassProvider
     */
    public function testResultListWith1Result(string $fqcn): void
    {
        $client = static::createClient();

        $client->request('GET', sprintf('%s?size=1', $this->getApiEndpointForFqcn($fqcn)));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($client->getResponse()->getContent(), $fqcn);

        $this->assertCount(1, $resultList);
    }

    /**
     * @dataProvider apiClassProvider
     * @testdox Calling size=0 will default to 10 results.
     */
    public function testResultListWithSize0Returning5Results(string $fqcn): void
    {
        $client = static::createClient();

        $client->request('GET', sprintf('%s?size=0', $this->getApiEndpointForFqcn($fqcn)));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($client->getResponse()->getContent(), $fqcn);

        $this->assertCount(10, $resultList);
    }

    /**
     * @dataProvider apiClassProvider
     * @testdox Calling size=-1 will default to 10 results.
     */
    public function testResultListWithNegativeParameter(string $fqcn): void
    {
        $client = static::createClient();

        $client->request('GET', sprintf('%s?size=-1', $this->getApiEndpointForFqcn($fqcn)));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($client->getResponse()->getContent(), $fqcn);

        $this->assertCount(10, $resultList);
    }

    /**
     * @dataProvider apiClassProvider
     * @testdox Using strings as parameter value will default to 10 results.
     */
    public function testResultListWithInvalidParameter(string $fqcn): void
    {
        $client = static::createClient();

        $client->request('GET', sprintf('%s?size=abc', $this->getApiEndpointForFqcn($fqcn)));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($client->getResponse()->getContent(), $fqcn);

        $this->assertCount(10, $resultList);
    }

    public function apiClassProvider(): array
    {
        return [
            [City::class,],
            [Ride::class,],
            [Photo::class,],
        ];
    }
}
