<?php declare(strict_types=1);

namespace Tests\Controller\Api\Parameter;

use App\Entity\City;
use App\Entity\Photo;
use App\Entity\Ride;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class OrderByParameterTest extends AbstractApiControllerTestCase
{
    #[DataProvider('apiClassProvider')]
    public function testResultListOrderByAscending(string $fqcn, string $propertyName): void
    {

        $this->client->request('GET', sprintf('%s?orderBy=%s&orderDirection=ASC', $this->getApiEndpointForFqcn($fqcn), $propertyName));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($this->client->getResponse()->getContent(), $fqcn);

        // Default size is 10, but we may have fewer records in fixtures
        $this->assertLessThanOrEqual(10, count($resultList));
        $this->assertNotEmpty($resultList);

        $getMethodName = sprintf('get%s', ucfirst($propertyName));

        $minPropertyValue = null;

        foreach ($resultList as $result) {
            if ($minPropertyValue) {
                $this->assertLessThanOrEqual($result->$getMethodName(), $minPropertyValue);
            }

            $minPropertyValue = $result->$getMethodName();
        }
    }

    #[DataProvider('apiClassProvider')]
    public function testResultListOrderByDescending(string $fqcn, string $propertyName): void
    {

        $this->client->request('GET', sprintf('%s?orderBy=%s&orderDirection=DESC', $this->getApiEndpointForFqcn($fqcn), $propertyName));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($this->client->getResponse()->getContent(), $fqcn);

        // Default size is 10, but we may have fewer records in fixtures
        $this->assertLessThanOrEqual(10, count($resultList));
        $this->assertNotEmpty($resultList);

        $getMethodName = sprintf('get%s', ucfirst($propertyName));

        $maxPropertyValue = null;

        foreach ($resultList as $result) {
            if ($maxPropertyValue) {
                $this->assertGreaterThanOrEqual($result->$getMethodName(), $maxPropertyValue);
            }

            $maxPropertyValue = $result->$getMethodName();
        }
    }

    #[DataProvider('apiClassProvider')]
    public function testResultListOrderByInvalidDirection(string $fqcn, string $propertyName): void
    {

        $this->client->request('GET', sprintf('%s?orderBy=%s&orderDirection=FOO', $this->getApiEndpointForFqcn($fqcn), $propertyName));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($this->client->getResponse()->getContent(), $fqcn);

        // Default size is 10, but we may have fewer records in fixtures
        $this->assertLessThanOrEqual(10, count($resultList));
        $this->assertNotEmpty($resultList);
    }

    #[DataProvider('apiClassProvider')]
    public function testResultListOrderByInvalidProperty(string $fqcn, string $propertyName): void
    {

        $this->client->request('GET', sprintf('%s?orderBy=invalidField&orderDirection=DESC', $this->getApiEndpointForFqcn($fqcn)));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($this->client->getResponse()->getContent(), $fqcn);

        // Default size is 10, but we may have fewer records in fixtures
        $this->assertLessThanOrEqual(10, count($resultList));
        $this->assertNotEmpty($resultList);
    }

    public static function apiClassProvider(): array
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
