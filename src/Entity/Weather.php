<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="weather")
 * @ORM\Entity(repositoryClass="App\Repository\WeatherRepository")
 * @JMS\ExclusionPolicy("all")
 */
class Weather
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose()
     */
    protected ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="Ride", inversedBy="weather")
     * @ORM\JoinColumn(name="ride_id", referencedColumnName="id")
     */
    protected ?Ride $ride = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose()
     */
    protected ?string $json = null;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Expose()
     */
    protected ?\DateTime $weatherDateTime = null;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Expose()
     */
    protected ?\DateTime $creationDateTime = null;

    /**
     * @ORM\Column(type="float")
     * @JMS\Expose()
     */
    protected ?float $temperatureMin = null;

    /**
     * @ORM\Column(type="float")
     * @JMS\Expose()
     */
    protected ?float $temperatureMax = null;

    /**
     * @ORM\Column(type="float")
     * @JMS\Expose()
     */
    protected ?float $temperatureMorning = null;

    /**
     * @ORM\Column(type="float")
     * @JMS\Expose()
     */
    protected ?float $temperatureDay = null;

    /**
     * @ORM\Column(type="float")
     * @JMS\Expose()
     */
    protected ?float $temperatureEvening = null;

    /**
     * @ORM\Column(type="float")
     * @JMS\Expose()
     */
    protected ?float $temperatureNight = null;

    /**
     * @ORM\Column(type="float")
     * @JMS\Expose()
     */
    protected ?float $pressure = null;

    /**
     * @ORM\Column(type="float")
     * @JMS\Expose()
     */
    protected ?float $humidity = null;

    /**
     * @ORM\Column(type="integer")
     * @JMS\Expose()
     */
    protected ?int $weatherCode = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Expose()
     */
    protected ?string $weather = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Expose()
     */
    protected ?string $weatherDescription = null;

    /**
     * @ORM\Column(type="string", length=5)
     * @JMS\Expose()
     */
    protected ?string $weatherIcon = null;

    /**
     * @ORM\Column(type="float")
     * @JMS\Expose()
     */
    protected ?float $windSpeed = null;

    /**
     * @ORM\Column(type="float")
     * @JMS\Expose()
     */
    protected ?float $windDirection = null;

    /**
     * @ORM\Column(type="float")
     * @JMS\Expose()
     */
    protected ?float $clouds = null;

    /**
     * @ORM\Column(type="float")
     * @JMS\Expose()
     */
    protected ?float $precipitation = null;

    public function __construct()
    {
        $this->creationDateTime = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJson(): ?string
    {
        return $this->json;
    }

    public function setJson(string $json = null): Weather
    {
        $this->json = $json;

        return $this;
    }

    public function getWeatherDateTime(): ?\DateTime
    {
        return $this->weatherDateTime;
    }

    public function setWeatherDateTime(\DateTime $weatherDateTime = null): Weather
    {
        $this->weatherDateTime = $weatherDateTime;

        return $this;
    }

    public function getCreationDateTime(): ?\DateTime
    {
        return $this->creationDateTime;
    }

    public function setCreationDateTime(\DateTime $creationDateTime = null): Weather
    {
        $this->creationDateTime = $creationDateTime;

        return $this;
    }

    public function getTemperatureMin(): ?float
    {
        return $this->temperatureMin;
    }

    public function setTemperatureMin(float $temperatureMin = null): Weather
    {
        $this->temperatureMin = $temperatureMin;

        return $this;
    }

    public function getTemperatureMax(): ?float
    {
        return $this->temperatureMax;
    }

    public function setTemperatureMax(float $temperatureMax = null): Weather
    {
        $this->temperatureMax = $temperatureMax;

        return $this;
    }

    public function getTemperatureMorning(): ?float
    {
        return $this->temperatureMorning;
    }

    public function setTemperatureMorning(float $temperatureMorning = null): Weather
    {
        $this->temperatureMorning = $temperatureMorning;

        return $this;
    }

    public function getTemperatureDay(): ?float
    {
        return $this->temperatureDay;
    }

    public function setTemperatureDay(float $temperatureDay = null): Weather
    {
        $this->temperatureDay = $temperatureDay;

        return $this;
    }

    public function getTemperatureEvening(): ?float
    {
        return $this->temperatureEvening;
    }

    public function setTemperatureEvening(float $temperatureEvening = null): Weather
    {
        $this->temperatureEvening = $temperatureEvening;

        return $this;
    }

    public function getTemperatureNight(): ?float
    {
        return $this->temperatureNight;
    }

    public function setTemperatureNight(float $temperatureNight = null): Weather
    {
        $this->temperatureNight = $temperatureNight;

        return $this;
    }

    public function getPressure(): ?float
    {
        return $this->pressure;
    }

    public function setPressure(float $pressure = null): Weather
    {
        $this->pressure = $pressure;

        return $this;
    }

    public function getHumidity(): ?float
    {
        return $this->humidity;
    }

    public function setHumidity(float $humidity = null): Weather
    {
        $this->humidity = $humidity;

        return $this;
    }

    public function getWeatherCode(): ?int
    {
        return $this->weatherCode;
    }

    public function setWeatherCode(int $weatherCode = null): Weather
    {
        $this->weatherCode = $weatherCode;

        return $this;
    }

    public function getWeather(): ?string
    {
        return $this->weather;
    }

    public function setWeather(string $weather = null): Weather
    {
        $this->weather = $weather;

        return $this;
    }

    public function getWeatherDescription(): ?string
    {
        return $this->weatherDescription;
    }

    public function setWeatherDescription(string $weatherDescription = null): Weather
    {
        $this->weatherDescription = $weatherDescription;

        return $this;
    }

    public function getWindSpeed(): ?float
    {
        return $this->windSpeed;
    }

    public function setWindSpeed(float $windSpeed = null): Weather
    {
        $this->windSpeed = $windSpeed;

        return $this;
    }

    public function getWindDirection(): ?float
    {
        return $this->windDirection;
    }

    public function setWindDirection(float $windDirection = null): Weather
    {
        $this->windDirection = $windDirection;

        return $this;
    }

    public function getClouds(): ?float
    {
        return $this->clouds;
    }

    public function setClouds(float $clouds = null): Weather
    {
        $this->clouds = $clouds;

        return $this;
    }

    public function getPrecipitation(): ?float
    {
        return $this->precipitation;
    }

    public function setPrecipitation(float $precipitation = null): Weather
    {
        $this->precipitation = $precipitation;

        return $this;
    }

    public function getRide(): ?Ride
    {
        return $this->ride;
    }

    public function setRide(Ride $ride = null): Weather
    {
        $this->ride = $ride;

        return $this;
    }

    public function getWeatherIcon(): ?string
    {
        return $this->weatherIcon;
    }

    public function setWeatherIcon(string $weatherIcon = null): Weather
    {
        $this->weatherIcon = $weatherIcon;

        return $this;
    }
}
