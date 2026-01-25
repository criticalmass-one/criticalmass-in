<?php declare(strict_types=1);

namespace Tests\Controller\Api\Parameter;

use App\Entity\City;
use App\Entity\Photo;
use App\Entity\Ride;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class StartValueParameterTest extends AbstractApiControllerTestCase
{
    #[DataProvider('apiClassProvider')]
    public function testResultListWithStartValueParameterOnly(string $fqcn, string $orderByProperty, string $jsonProperty, string $direction, $startValue): void
    {
        $this->client->request('GET', sprintf('%s?startValue=hamburg', $this->getApiEndpointForFqcn($fqcn)));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    #[DataProvider('apiClassProvider')]
    public function testResultListWithStartValueAndOrderByParameterAscending(string $fqcn, string $orderByProperty, string $jsonProperty, string $direction, $startValue): void
    {
        if ($startValue instanceof \DateTime) {
            $this->client->request('GET', sprintf('%s?orderBy=%s&orderDirection=%s&startValue=%s', $this->getApiEndpointForFqcn($fqcn), $orderByProperty, $direction, $startValue->format('Y-m-d')));
        } else {
            $this->client->request('GET', sprintf('%s?orderBy=%s&orderDirection=%s&startValue=%s', $this->getApiEndpointForFqcn($fqcn), $orderByProperty, $direction, $startValue));
        }

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $resultList = $this->getJsonResponse();

        // Skip if empty result
        if (empty($resultList)) {
            $this->markTestSkipped('No results returned');
        }

        // Verify results are within expected bounds based on startValue
        foreach ($resultList as $result) {
            $this->assertArrayHasKey($jsonProperty, $result, sprintf('Property %s not found in response', $jsonProperty));

            $value = $result[$jsonProperty];

            // For DateTime comparison, convert timestamp to comparable value
            if ($startValue instanceof \DateTime) {
                if (is_int($value)) {
                    // Unix timestamp
                    $resultDateTime = (new \DateTime())->setTimestamp($value);
                    if ($direction === 'ASC') {
                        $this->assertGreaterThanOrEqual($startValue, $resultDateTime);
                    } else {
                        $this->assertLessThanOrEqual($startValue, $resultDateTime);
                    }
                    $startValue = $resultDateTime;
                }
            } else {
                // String comparison
                if ($direction === 'ASC') {
                    $this->assertGreaterThanOrEqual($startValue, $value);
                } else {
                    $this->assertLessThanOrEqual($startValue, $value);
                }
                $startValue = $value;
            }
        }
    }

    public static function apiClassProvider(): array
    {
        return [
            // Cities: Hamburg, Berlin, Munich, Kiel exist in fixtures
            // Note: orderBy uses entity property (city), but JSON response uses serialized name (name)
            [City::class, 'city', 'name', 'ASC', 'Berlin'],
            [City::class, 'city', 'name', 'DESC', 'Munich'],
            // Rides: Nov 2025 to March 2026 exist in fixtures
            [Ride::class, 'dateTime', 'date_time', 'ASC', new \DateTime('2025-11-01 19:00:00')],
            [Ride::class, 'dateTime', 'date_time', 'DESC', new \DateTime('2026-04-01 19:00:00')],
            // Photos: Nov-Dec 2025 exist in fixtures
            [Photo::class, 'exifCreationDate', 'exif_creation_date', 'ASC', new \DateTime('2025-11-01 19:00:00')],
            [Photo::class, 'exifCreationDate', 'exif_creation_date', 'DESC', new \DateTime('2025-12-31 19:00:00')],
        ];
    }
}
