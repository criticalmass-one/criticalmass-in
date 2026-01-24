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
    public function testResultListWithStartValueAndOrderByParameterAscending(string $fqcn, string $propertyUnterTest, string $direction, $startValue): void
    {
        $client = static::createClient();

        if ($startValue instanceof \DateTime) {
            $client->request('GET', sprintf('%s?orderBy=%s&orderDirection=%s&startValue=%s', $this->getApiEndpointForFqcn($fqcn), $propertyUnterTest, $direction, $startValue->format('Y-m-d')));
        } else {
            $client->request('GET', sprintf('%s?orderBy=%s&orderDirection=%s&startValue=%s', $this->getApiEndpointForFqcn($fqcn), $propertyUnterTest, $direction, $startValue));
        }

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($client->getResponse()->getContent(), $fqcn);

        // Verify results are within expected bounds based on startValue
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
            // Cities: Hamburg, Berlin, Munich, Kiel exist in fixtures
            [City::class, 'city', 'ASC', 'Berlin'],
            [City::class, 'city', 'DESC', 'Munich'],
            // Rides: Nov 2025 to March 2026 exist in fixtures
            [Ride::class, 'dateTime', 'ASC', new \DateTime('2025-11-01 19:00:00')],
            [Ride::class, 'dateTime', 'DESC', new \DateTime('2026-04-01 19:00:00')],
            // Photos: Nov-Dec 2025 exist in fixtures
            [Photo::class, 'exifCreationDate', 'ASC', new \DateTime('2025-11-01 19:00:00')],
            [Photo::class, 'exifCreationDate', 'DESC', new \DateTime('2025-12-31 19:00:00')],
        ];
    }
}
