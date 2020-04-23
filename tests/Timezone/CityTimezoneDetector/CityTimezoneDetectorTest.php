<?php declare(strict_types=1);

namespace Tests\Timezone\CityTimezoneDetector;

use App\Criticalmass\Timezone\CityTimezoneDetector\CityTimezoneDetector;
use App\Entity\City;
use Curl\Curl;
use PHPUnit\Framework\TestCase;

class CityTimezoneDetectorTest extends TestCase
{
    public function testNoQueryWithoutLatLng(): void
    {
        $curl = $this->createMock(Curl::class);
        $curl
            ->expects($this->never())
            ->method('get');

        $cityTimezoneDetector = new CityTimezoneDetector('TEST_KEY');

        $property = new \ReflectionProperty($cityTimezoneDetector, 'curl');
        $property->setAccessible(true);
        $property->setValue($cityTimezoneDetector, $curl);

        $city = new City();

        $timezone = $cityTimezoneDetector->queryForCity($city);

        $this->assertNull($timezone);
    }

    public function testQueryStringBuilder(): void
    {
        $curl = $this->createMock(Curl::class);
        $curl
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo('http://api.timezonedb.com/v2/get-time-zone?key=TEST_KEY&by=position&format=json&lat=53.5&lng=10.5'));

        $cityTimezoneDetector = new CityTimezoneDetector('TEST_KEY');

        $property = new \ReflectionProperty($cityTimezoneDetector, 'curl');
        $property->setAccessible(true);
        $property->setValue($cityTimezoneDetector, $curl);

        $city = new City();
        $city
            ->setLatitude(53.5)
            ->setLongitude(10.5);

        $cityTimezoneDetector->queryForCity($city);
    }

    public function testAssignedTimezone(): void
    {
        $curl = $this->createMock(Curl::class);
        $curl->method('get');
        $curl->response = new \stdClass();
        $curl->httpStatusCode = 200;
        $curl->response->zoneName = 'Europe/Berlin';

        $cityTimezoneDetector = new CityTimezoneDetector('TEST_KEY');

        $property = new \ReflectionProperty($cityTimezoneDetector, 'curl');
        $property->setAccessible(true);
        $property->setValue($cityTimezoneDetector, $curl);

        $city = new City();
        $city
            ->setLatitude(53.5)
            ->setLongitude(10.5);

        $timezone = $cityTimezoneDetector->queryForCity($city);

        $this->assertEquals('Europe/Berlin', $timezone);
    }

    public function testWrongApiKey(): void
    {
        $curl = $this->createMock(Curl::class);
        $curl->method('get');
        $curl->response = new \stdClass();
        $curl->httpStatusCode = 403;

        $cityTimezoneDetector = new CityTimezoneDetector('TEST_KEY');

        $property = new \ReflectionProperty($cityTimezoneDetector, 'curl');
        $property->setAccessible(true);
        $property->setValue($cityTimezoneDetector, $curl);

        $city = new City();
        $city
            ->setLatitude(53.5)
            ->setLongitude(10.5);

        $timezone = $cityTimezoneDetector->queryForCity($city);

        $this->assertNull($timezone);
    }
}