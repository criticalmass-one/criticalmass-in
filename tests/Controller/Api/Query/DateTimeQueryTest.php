<?php declare(strict_types=1);

namespace Tests\Controller\Api\Query;

use App\Entity\Photo;
use App\Entity\Ride;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Controller\Api\AbstractApiControllerTest;

class DateTimeQueryTest extends AbstractApiControllerTest
{
    #[DataProvider('apiClassProvider')]
    public function testRideListWithParameter(string $fqcn, string $propertyName, array $query, string $dateTimePattern, string $expectedDateTimeString): void
    {
        $client = static::createClient();

        $client->request('GET', sprintf('%s?%s', $this->getApiEndpointForFqcn($fqcn), http_build_query($query)));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $resultList = $this->deserializeEntityList($client->getResponse()->getContent(), $fqcn);

        $this->assertGreaterThan(0, count($resultList));

        $getMethodName = sprintf('get%s', ucfirst($propertyName));

        foreach ($resultList as $result) {
            $this->assertEquals($expectedDateTimeString, $result->$getMethodName()->format($dateTimePattern));
        }
    }

    public static function apiClassProvider(): array
    {
        return [
            [Ride::class, 'dateTime', ['year' => 2050], 'Y', '2050'],
            [Ride::class, 'dateTime', ['year' => 2016, 'month' => 2], 'Y-m', '2016-02'],
            [Ride::class, 'dateTime', ['year' => 2015, 'month' => 6, 'day' => 1], 'Y-m-d', '2015-06-01'],
            [Photo::class, 'exifCreationDate', ['year' => 2019], 'Y', '2019'],
            [Photo::class, 'exifCreationDate', ['year' => 2022, 'month' => 9], 'Y-m', '2022-09'],
            [Photo::class, 'exifCreationDate', ['year' => 2011, 'month' => 6, 'day' => 24], 'Y-m-d', '2011-06-24'],
        ];
    }
}
