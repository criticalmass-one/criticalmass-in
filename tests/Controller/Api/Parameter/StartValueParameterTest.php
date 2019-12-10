<?php declare(strict_types=1);

namespace Tests\Controller\Api\Parameter;

use App\Entity\City;
use App\Entity\Photo;
use App\Entity\Ride;
use Tests\Controller\Api\AbstractApiControllerTest;

class StartValueParameterTest extends AbstractApiControllerTest
{
    /**
     * @dataProvider apiClassProvider
     */
    public function testResultListWithStartValueParameterOnly(string $fqcn, string $propertyUnterTest, string $startValue): void
    {
        $client = static::createClient();

        $client->request('GET', sprintf('%s?startValue=hamburg', $this->getApiEndpointForFqcn($fqcn)));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider apiClassProvider
     */
    public function testResultListWithStartValueAndOrderByParameterAscending(string $fqcn, string $propertyUnterTest, string $startValue): void
    {
        $client = static::createClient();

        $client->request('GET', sprintf('%s?orderBy=%s&orderDirection=ASC&startValue=%s', $this->getApiEndpointForFqcn($fqcn), $propertyUnterTest, $startValue));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($client->getResponse()->getContent(), $fqcn);

        $this->assertCount(10, $resultList);

        $getMethodName = sprintf('get%s', ucfirst($propertyUnterTest));

        foreach ($resultList as $result) {
            $this->assertGreaterThanOrEqual($startValue, $result->$getMethodName());

            $startValue = $result->$getMethodName();
        }
    }

    /**
     * @dataProvider apiClassProvider
     */
    public function testResultListWithStartValueAndOrderByParameterDescending(string $fqcn, string $propertyUnterTest, string $startValue): void
    {
        $client = static::createClient();

        $client->request('GET', sprintf('%s?orderBy=%s&orderDirection=DESC&startValue=%s', $this->getApiEndpointForFqcn($fqcn), $propertyUnterTest, $startValue));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($client->getResponse()->getContent(), $fqcn);

        $this->assertCount(10, $resultList);

        $getMethodName = sprintf('get%s', ucfirst($propertyUnterTest));

        foreach ($resultList as $result) {
            $this->assertLessThanOrEqual($startValue, $result->$getMethodName());

            $startValue = $result->$getMethodName();
        }
    }

    public function apiClassProvider(): array
    {
        return [
            [City::class, 'title', 'Hamburg'],
            [Ride::class, 'dateTime', '2022-07-01'],
            [Photo::class, 'exifCreationDate', '2019-01-01'],
        ];
    }
}
