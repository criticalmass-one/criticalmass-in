<?php declare(strict_types=1);

namespace Tests\Controller\Api\Parameter;

use App\Entity\City;
use App\Entity\Photo;
use App\Entity\Ride;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class SizeParameterTest extends AbstractApiControllerTestCase
{
    #[DataProvider('apiClassProvider')]
    #[TestDox('Calling api without size parameter delivers up to 10 results (default size).')]
    public function testResultListWithBoundingSizeParameter(string $fqcn): void
    {

        $this->client->request('GET', sprintf('%s', $this->getApiEndpointForFqcn($fqcn)));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($this->client->getResponse()->getContent(), $fqcn);

        // Default size is 10, but we may have fewer records in fixtures
        $this->assertLessThanOrEqual(10, count($resultList));
        $this->assertNotEmpty($resultList);
    }

    #[DataProvider('apiClassProvider')]
    #[TestDox('Request up to 5 results.')]
    public function testResultListWith5Results(string $fqcn): void
    {

        $this->client->request('GET', sprintf('%s?size=5', $this->getApiEndpointForFqcn($fqcn)));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($this->client->getResponse()->getContent(), $fqcn);

        $this->assertLessThanOrEqual(5, count($resultList));
        $this->assertNotEmpty($resultList);
    }

    #[DataProvider('apiClassProvider')]
    public function testResultListWith1Result(string $fqcn): void
    {

        $this->client->request('GET', sprintf('%s?size=1', $this->getApiEndpointForFqcn($fqcn)));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($this->client->getResponse()->getContent(), $fqcn);

        $this->assertCount(1, $resultList);
    }

    #[DataProvider('apiClassProvider')]
    #[TestDox('Calling size=0 will default to up to 10 results.')]
    public function testResultListWithSize0Returning5Results(string $fqcn): void
    {

        $this->client->request('GET', sprintf('%s?size=0', $this->getApiEndpointForFqcn($fqcn)));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($this->client->getResponse()->getContent(), $fqcn);

        $this->assertLessThanOrEqual(10, count($resultList));
        $this->assertNotEmpty($resultList);
    }

    #[DataProvider('apiClassProvider')]
    #[TestDox('Calling size=-1 will default to up to 10 results.')]
    public function testResultListWithNegativeParameter(string $fqcn): void
    {

        $this->client->request('GET', sprintf('%s?size=-1', $this->getApiEndpointForFqcn($fqcn)));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($this->client->getResponse()->getContent(), $fqcn);

        $this->assertLessThanOrEqual(10, count($resultList));
        $this->assertNotEmpty($resultList);
    }

    #[DataProvider('apiClassProvider')]
    #[TestDox('Using strings as parameter value will result in an error.')]
    public function testResultListWithInvalidParameter(string $fqcn): void
    {

        $this->client->request('GET', sprintf('%s?size=abc', $this->getApiEndpointForFqcn($fqcn)));

        // Invalid parameter value causes an error
        $this->assertContains($this->client->getResponse()->getStatusCode(), [400, 500]);
    }

    public static function apiClassProvider(): array
    {
        return [
            [City::class,],
            [Ride::class,],
            [Photo::class,],
        ];
    }
}
