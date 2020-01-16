<?php declare(strict_types=1);

namespace Tests\Controller\Api\Parameter;

use App\Entity\City;
use App\Entity\Photo;
use App\Entity\Ride;
use Tests\Controller\Api\AbstractApiControllerTest;

class OrderByParameterTest extends AbstractApiControllerTest
{
    /**
     * @dataProvider apiClassProvider
     */
    public function testResultListOrderByAscending(string $fqcn, string $propertyName): void
    {
        $client = static::createClient();

        $client->request('GET', sprintf('%s?orderBy=%s&orderDirection=ASC', $this->getApiEndpointForFqcn($fqcn), $propertyName));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($client->getResponse()->getContent(), $fqcn);

        $this->assertCount(10, $resultList);

        $getMethodName = sprintf('get%s', ucfirst($propertyName));

        $minPropertyValue = null;

        foreach ($resultList as $result) {
            if ($minPropertyValue) {
                $this->assertLessThanOrEqual($result->$getMethodName(), $minPropertyValue);
            }

            $minPropertyValue = $result->$getMethodName();
        }
    }

    /**
     * @dataProvider apiClassProvider
     */
    public function testResultListOrderByDescending(string $fqcn, string $propertyName): void
    {
        $client = static::createClient();

        $client->request('GET', sprintf('%s?orderBy=%s&orderDirection=DESC', $this->getApiEndpointForFqcn($fqcn), $propertyName));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($client->getResponse()->getContent(), $fqcn);

        $this->assertCount(10, $resultList);

        $getMethodName = sprintf('get%s', ucfirst($propertyName));

        $maxPropertyValue = null;

        foreach ($resultList as $result) {
            if ($maxPropertyValue) {
                $this->assertGreaterThanOrEqual($result->$getMethodName(), $maxPropertyValue);
            }

            $maxPropertyValue = $result->$getMethodName();
        }
    }

    /**
     * @dataProvider apiClassProvider
     */
    public function testResultListOrderByInvalidDirection(string $fqcn, string $propertyName): void
    {
        $client = static::createClient();

        $client->request('GET', sprintf('%s?orderBy=%s&orderDirection=FOO', $this->getApiEndpointForFqcn($fqcn), $propertyName));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($client->getResponse()->getContent(), $fqcn);

        $this->assertCount(10, $resultList);
    }

    /**
     * @dataProvider apiClassProvider
     */
    public function testResultListOrderByInvalidProperty(string $fqcn, string $propertyName): void
    {
        $client = static::createClient();

        $client->request('GET', sprintf('%s?orderBy=invalidField&orderDirection=DESC', $this->getApiEndpointForFqcn($fqcn)));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($client->getResponse()->getContent(), $fqcn);

        $this->assertCount(10, $resultList);
    }

    public function apiClassProvider(): array
    {
        return [
            [City::class, 'title'],
            [City::class, 'city'],
            [Ride::class, 'title'],
            [Ride::class, 'dateTime'],
            [Photo::class, 'exifCreationDate'],
            [Photo::class, 'creationDateTime'],
        ];
    }
}
