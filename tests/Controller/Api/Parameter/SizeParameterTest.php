<?php declare(strict_types=1);

namespace Tests\Controller\Api\Parameter;

use App\Entity\City;
use App\Entity\Photo;
use App\Entity\Ride;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Controller\Api\AbstractApiControllerTest;

class SizeParameterTest extends AbstractApiControllerTest
{
    #[DataProvider('apiClassProvider')]
    #[TestDox('Calling api without size parameter delivers 10 results.')]
    public function testResultListWithBoundingSizeParameter(string $fqcn): void
    {
        $client = static::createClient();

        $client->request('GET', sprintf('%s', $this->getApiEndpointForFqcn($fqcn)));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($client->getResponse()->getContent(), $fqcn);

        $this->assertCount(10, $resultList);
    }

    #[DataProvider('apiClassProvider')]
    #[TestDox('Request 5 results.')]
    public function testResultListWith5Results(string $fqcn): void
    {
        $client = static::createClient();

        $client->request('GET', sprintf('%s?size=5', $this->getApiEndpointForFqcn($fqcn)));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($client->getResponse()->getContent(), $fqcn);

        $this->assertCount(5, $resultList);
    }

    #[DataProvider('apiClassProvider')]
    public function testResultListWith1Result(string $fqcn): void
    {
        $client = static::createClient();

        $client->request('GET', sprintf('%s?size=1', $this->getApiEndpointForFqcn($fqcn)));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($client->getResponse()->getContent(), $fqcn);

        $this->assertCount(1, $resultList);
    }

    #[DataProvider('apiClassProvider')]
    #[TestDox('Calling size=0 will default to 10 results.')]
    public function testResultListWithSize0Returning5Results(string $fqcn): void
    {
        $client = static::createClient();

        $client->request('GET', sprintf('%s?size=0', $this->getApiEndpointForFqcn($fqcn)));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($client->getResponse()->getContent(), $fqcn);

        $this->assertCount(10, $resultList);
    }

    #[DataProvider('apiClassProvider')]
    #[TestDox('Calling size=-1 will default to 10 results.')]
    public function testResultListWithNegativeParameter(string $fqcn): void
    {
        $client = static::createClient();

        $client->request('GET', sprintf('%s?size=-1', $this->getApiEndpointForFqcn($fqcn)));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($client->getResponse()->getContent(), $fqcn);

        $this->assertCount(10, $resultList);
    }

    #[DataProvider('apiClassProvider')]
    #[TestDox('Using strings as parameter value will default to 10 results.')]
    public function testResultListWithInvalidParameter(string $fqcn): void
    {
        $client = static::createClient();

        $client->request('GET', sprintf('%s?size=abc', $this->getApiEndpointForFqcn($fqcn)));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($client->getResponse()->getContent(), $fqcn);

        $this->assertCount(10, $resultList);
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
