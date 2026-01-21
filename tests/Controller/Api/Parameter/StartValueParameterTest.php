<?php declare(strict_types=1);

namespace Tests\Controller\Api\Parameter;

use App\Entity\City;
use App\Entity\Photo;
use App\Entity\Ride;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Controller\Api\AbstractApiControllerTest;

class StartValueParameterTest extends AbstractApiControllerTest
{
    #[DataProvider('apiClassProvider')]
    public function testResultListWithStartValueParameterOnly(string $fqcn, string $propertyUnterTest, $start): void
    {
        $client = static::createClient();

        $client->request('GET', sprintf('%s?startValue=hamburg', $this->getApiEndpointForFqcn($fqcn)));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    #[DataProvider('apiClassProvider')]
    public function testResultListWithStartValueAndOrderByParameterAscending(string $fqcn, string $propertyUnterTest, string $direction, int $expectedResults, $startValue): void
    {
        $client = static::createClient();

        if ($startValue instanceof \DateTime) {
            $client->request('GET', sprintf('%s?orderBy=%s&orderDirection=%s&startValue=%s', $this->getApiEndpointForFqcn($fqcn), $propertyUnterTest, $direction, $startValue->format('Y-m-d')));
        } else {
            $client->request('GET', sprintf('%s?orderBy=%s&orderDirection=%s&startValue=%s', $this->getApiEndpointForFqcn($fqcn), $propertyUnterTest, $direction, $startValue));
        }

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($client->getResponse()->getContent(), $fqcn);

        $this->assertCount($expectedResults, $resultList);

        $getMethodName = sprintf('get%s', ucfirst($propertyUnterTest));

        foreach ($resultList as $result) {
            if ($direction === 'ASC') {
                $this->assertGreaterThanOrEqual($startValue, $result->$getMethodName());
            } else {
                $this->assertLessThanOrEqual($startValue, $result->$getMethodName());
            }

            $startValue = $result->$getMethodName();
        }
    }

    public static function apiClassProvider(): array
    {
        return [
            [City::class, 'city', 'ASC', 10, 'Hamburg'],
            [City::class, 'city', 'ASC', 3, 'Schwerin'],
            [City::class, 'city', 'DESC', 6, 'Hamburg'],
            [City::class, 'city', 'DESC', 3, 'Dresden'],
            [Ride::class, 'dateTime', 'ASC', 10, new \DateTime('2022-07-01 19:00:00')],
            [Ride::class, 'dateTime', 'DESC', 10, new \DateTime('2022-07-01 19:00:00')],
            [Ride::class, 'dateTime', 'DESC', 2, new \DateTime('2011-06-24 19:00:00')],
            [Ride::class, 'dateTime', 'DESC', 1, new \DateTime('2011-06-23 19:00:00')],
            [Photo::class, 'exifCreationDate', 'ASC', 10, new \DateTime('2019-01-01 19:00:00')],
            [Photo::class, 'exifCreationDate', 'DESC', 10, new \DateTime('2019-01-01 19:00:00')],
            [Photo::class, 'exifCreationDate', 'DESC', 0, new \DateTime('2011-06-23 19:00:00')],
        ];
    }
}
