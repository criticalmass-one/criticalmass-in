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
    public function testResultListOrderByAscending(string $fqcn, string $propertyName, string $jsonPropertyName): void
    {
        $this->client->request('GET', sprintf('%s?orderBy=%s&orderDirection=ASC', $this->getApiEndpointForFqcn($fqcn), $propertyName));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $resultList = $this->getJsonResponse();

        // Default size is 10, but we may have fewer records in fixtures
        $this->assertIsArray($resultList);
        $this->assertLessThanOrEqual(10, count($resultList));
        $this->assertNotEmpty($resultList);

        $minPropertyValue = null;

        foreach ($resultList as $result) {
            $value = $result[$jsonPropertyName];
            if ($minPropertyValue !== null) {
                $this->assertLessThanOrEqual($value, $minPropertyValue);
            }

            $minPropertyValue = $value;
        }
    }

    #[DataProvider('apiClassProvider')]
    public function testResultListOrderByDescending(string $fqcn, string $propertyName, string $jsonPropertyName): void
    {
        $this->client->request('GET', sprintf('%s?orderBy=%s&orderDirection=DESC', $this->getApiEndpointForFqcn($fqcn), $propertyName));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $resultList = $this->getJsonResponse();

        // Default size is 10, but we may have fewer records in fixtures
        $this->assertIsArray($resultList);
        $this->assertLessThanOrEqual(10, count($resultList));
        $this->assertNotEmpty($resultList);

        $maxPropertyValue = null;

        foreach ($resultList as $result) {
            $value = $result[$jsonPropertyName];
            if ($maxPropertyValue !== null) {
                $this->assertGreaterThanOrEqual($value, $maxPropertyValue);
            }

            $maxPropertyValue = $value;
        }
    }

    #[DataProvider('apiClassProvider')]
    public function testResultListOrderByInvalidDirection(string $fqcn, string $propertyName, string $jsonPropertyName): void
    {
        $this->client->request('GET', sprintf('%s?orderBy=%s&orderDirection=FOO', $this->getApiEndpointForFqcn($fqcn), $propertyName));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $resultList = $this->getJsonResponse();

        // Default size is 10, but we may have fewer records in fixtures
        $this->assertIsArray($resultList);
        $this->assertLessThanOrEqual(10, count($resultList));
        $this->assertNotEmpty($resultList);
    }

    #[DataProvider('apiClassProvider')]
    public function testResultListOrderByInvalidProperty(string $fqcn, string $propertyName, string $jsonPropertyName): void
    {
        $this->client->request('GET', sprintf('%s?orderBy=invalidField&orderDirection=DESC', $this->getApiEndpointForFqcn($fqcn)));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $resultList = $this->getJsonResponse();

        // Default size is 10, but we may have fewer records in fixtures
        $this->assertIsArray($resultList);
        $this->assertLessThanOrEqual(10, count($resultList));
        $this->assertNotEmpty($resultList);
    }

    public static function apiClassProvider(): array
    {
        return [
            // Entity property name => JSON property name (snake_case)
            [City::class, 'title', 'title'],
            [City::class, 'city', 'name'],  // city property serializes to 'name'
            [Ride::class, 'title', 'title'],
            [Ride::class, 'dateTime', 'date_time'],
            [Photo::class, 'exifCreationDate', 'exif_creation_date'],
            [Photo::class, 'creationDateTime', 'creation_date_time'],
        ];
    }
}
