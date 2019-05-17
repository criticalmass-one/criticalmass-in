<?php declare(strict_types=1);

namespace Tests\Weather;

use App\Criticalmass\Weather\WeatherFactory\WeatherFactory;
use App\Entity\Ride;
use Cmfcmf\OpenWeatherMap\WeatherForecast;
use PHPUnit\Framework\TestCase;

class WeatherFactoryTest extends TestCase
{
    /** @var \SimpleXMLElement $fakeXml */
    protected $fakeXml;

    /** @var WeatherForecast $forecast */
    protected $forecast;

    protected function setUp()
    {
        $this->fakeXml = new \SimpleXMLElement(FakeData::forecastXML());
        $this->forecast = new WeatherForecast($this->fakeXml, 'Berlin', 2);
    }

    public function test1(): void
    {
        $weatherFactory = new WeatherFactory();
        $ride = new Ride();

        $weather = $weatherFactory->createWeather($this->forecast->current());
        $weather->setRide($ride);

        $this->assertEquals(40.59, $weather->getTemperatureMin());
        $this->assertEquals(41.0, $weather->getTemperatureMax());
        $this->assertEquals(41.0, $weather->getTemperatureMorning());
        $this->assertEquals(41.0, $weather->getTemperatureDay());
        $this->assertEquals(41.0, $weather->getTemperatureEvening());
        $this->assertEquals(40.59, $weather->getTemperatureNight());

        $this->assertEquals(1048.25, $weather->getPressure());
        $this->assertEquals(97.0, $weather->getHumidity());
        $this->assertEquals(500, $weather->getWeatherCode());
        $this->assertEquals('light rain', $weather->getWeatherDescription());
        $this->assertEquals('10d', $weather->getWeatherIcon());
        $this->assertEquals(4.38, $weather->getWindSpeed());
        $this->assertEquals(315.0, $weather->getWindDirection());
        $this->assertEquals(92.0, $weather->getClouds());
        $this->assertEquals(0.25, $weather->getPrecipitation());
    }
}
