<?php declare(strict_types=1);

namespace Tests\Controller\Api\Query;

use App\Entity\Photo;
use App\Entity\Ride;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Controller\Api\AbstractApiControllerTestCase;

class DateTimeQueryTest extends AbstractApiControllerTestCase
{
    #[DataProvider('apiClassProvider')]
    public function testRideListWithParameter(string $fqcn, string $propertyName, array $query, string $dateTimePattern, string $expectedDateTimeString): void
    {

        $this->client->request('GET', sprintf('%s?%s', $this->getApiEndpointForFqcn($fqcn), http_build_query($query)));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($this->client->getResponse()->getContent(), $fqcn);

        $this->assertGreaterThan(0, count($resultList));

        $getMethodName = sprintf('get%s', ucfirst($propertyName));

        foreach ($resultList as $result) {
            $this->assertEquals($expectedDateTimeString, $result->$getMethodName()->format($dateTimePattern));
        }
    }

    public static function apiClassProvider(): array
    {
        // Use dates that exist in fixtures:
        // Rides: Nov 2025 - Mar 2026 (2025-11-23, 2025-12-23, 2026-02-23, 2026-03-23)
        // Photos: Nov-Dec 2025 (2025-11-23, 2025-12-23)
        return [
            [Ride::class, 'dateTime', ['year' => 2025], 'Y', '2025'],
            [Ride::class, 'dateTime', ['year' => 2026, 'month' => 2], 'Y-m', '2026-02'],
            [Ride::class, 'dateTime', ['year' => 2025, 'month' => 12, 'day' => 23], 'Y-m-d', '2025-12-23'],
            [Photo::class, 'exifCreationDate', ['year' => 2025], 'Y', '2025'],
            [Photo::class, 'exifCreationDate', ['year' => 2025, 'month' => 12], 'Y-m', '2025-12'],
            [Photo::class, 'exifCreationDate', ['year' => 2025, 'month' => 12, 'day' => 23], 'Y-m-d', '2025-12-23'],
        ];
    }
}
