<?php

namespace Caldera\Bundle\CalderaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="weather")
 * @ORM\Entity(repositoryClass="Caldera\Bundle\CalderaBundle\Repository\WeatherRepository")
 */
class Weather {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Ride", inversedBy="weather")
     * @ORM\JoinColumn(name="ride_id", referencedColumnName="id")
     */
    protected $ride;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $json;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $weatherDateTime;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $creationDateTime;

    /**
     * @ORM\Column(type="float")
     */
    protected $temperatureMin;

    /**
     * @ORM\Column(type="float")
     */
    protected $temperatureMax;

    /**
     * @ORM\Column(type="float")
     */
    protected $temperatureMorning;

    /**
     * @ORM\Column(type="float")
     */
    protected $temperatureDay;

    /**
     * @ORM\Column(type="float")
     */
    protected $temperatureEvening;

    /**
     * @ORM\Column(type="float")
     */
    protected $temperatureNight;

    /**
     * @ORM\Column(type="float")
     */
    protected $pressure;

    /**
     * @ORM\Column(type="float")
     */
    protected $humidity;

    /**
     * @ORM\Column(type="integer")
     */
    protected $weatherCode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $weather;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $weatherDescription;

    /**
     * @ORM\Column(type="string", length=5)
     */
    protected $weatherIcon;

    /**
     * @ORM\Column(type="float")
     */
    protected $windSpeed;

    /**
     * @ORM\Column(type="integer")
     */
    protected $windDeg;

    /**
     * @ORM\Column(type="integer")
     */
    protected $clouds;

    /**
     * @ORM\Column(type="float")
     */
    protected $rain;

    public function __construct()
    {
        $this->creationDateTime = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set json
     *
     * @param string $json
     * @return Weather
     */
    public function setJson($json)
    {
        $this->json = $json;

        return $this;
    }

    /**
     * Get json
     *
     * @return string 
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     * Set weatherDateTime
     *
     * @param \DateTime $weatherDateTime
     * @return Weather
     */
    public function setWeatherDateTime($weatherDateTime)
    {
        $this->weatherDateTime = $weatherDateTime;

        return $this;
    }

    /**
     * Get weatherDateTime
     *
     * @return \DateTime 
     */
    public function getWeatherDateTime()
    {
        return $this->weatherDateTime;
    }

    /**
     * Set creationDateTime
     *
     * @param \DateTime $creationDateTime
     * @return Weather
     */
    public function setCreationDateTime($creationDateTime)
    {
        $this->creationDateTime = $creationDateTime;

        return $this;
    }

    /**
     * Get creationDateTime
     *
     * @return \DateTime 
     */
    public function getCreationDateTime()
    {
        return $this->creationDateTime;
    }

    /**
     * Set temperatureMin
     *
     * @param float $temperatureMin
     * @return Weather
     */
    public function setTemperatureMin($temperatureMin)
    {
        $this->temperatureMin = $temperatureMin;

        return $this;
    }

    /**
     * Get temperatureMin
     *
     * @return float 
     */
    public function getTemperatureMin()
    {
        return $this->temperatureMin;
    }

    /**
     * Set temperatureMax
     *
     * @param float $temperatureMax
     * @return Weather
     */
    public function setTemperatureMax($temperatureMax)
    {
        $this->temperatureMax = $temperatureMax;

        return $this;
    }

    /**
     * Get temperatureMax
     *
     * @return float 
     */
    public function getTemperatureMax()
    {
        return $this->temperatureMax;
    }

    /**
     * Set temperatureMorning
     *
     * @param float $temperatureMorning
     * @return Weather
     */
    public function setTemperatureMorning($temperatureMorning)
    {
        $this->temperatureMorning = $temperatureMorning;

        return $this;
    }

    /**
     * Get temperatureMorning
     *
     * @return float 
     */
    public function getTemperatureMorning()
    {
        return $this->temperatureMorning;
    }

    /**
     * Set temperatureDay
     *
     * @param float $temperatureDay
     * @return Weather
     */
    public function setTemperatureDay($temperatureDay)
    {
        $this->temperatureDay = $temperatureDay;

        return $this;
    }

    /**
     * Get temperatureDay
     *
     * @return float 
     */
    public function getTemperatureDay()
    {
        return $this->temperatureDay;
    }

    /**
     * Set temperatureEvening
     *
     * @param float $temperatureEvening
     * @return Weather
     */
    public function setTemperatureEvening($temperatureEvening)
    {
        $this->temperatureEvening = $temperatureEvening;

        return $this;
    }

    /**
     * Get temperatureEvening
     *
     * @return float 
     */
    public function getTemperatureEvening()
    {
        return $this->temperatureEvening;
    }

    /**
     * Set temperatureNight
     *
     * @param float $temperatureNight
     * @return Weather
     */
    public function setTemperatureNight($temperatureNight)
    {
        $this->temperatureNight = $temperatureNight;

        return $this;
    }

    /**
     * Get temperatureNight
     *
     * @return float 
     */
    public function getTemperatureNight()
    {
        return $this->temperatureNight;
    }

    /**
     * Set pressure
     *
     * @param float $pressure
     * @return Weather
     */
    public function setPressure($pressure)
    {
        $this->pressure = $pressure;

        return $this;
    }

    /**
     * Get pressure
     *
     * @return float 
     */
    public function getPressure()
    {
        return $this->pressure;
    }

    /**
     * Set humidity
     *
     * @param float $humidity
     * @return Weather
     */
    public function setHumidity($humidity)
    {
        $this->humidity = $humidity;

        return $this;
    }

    /**
     * Get humidity
     *
     * @return float 
     */
    public function getHumidity()
    {
        return $this->humidity;
    }

    /**
     * Set weatherCode
     *
     * @param integer $weatherCode
     * @return Weather
     */
    public function setWeatherCode($weatherCode)
    {
        $this->weatherCode = $weatherCode;

        return $this;
    }

    /**
     * Get weatherCode
     *
     * @return integer 
     */
    public function getWeatherCode()
    {
        return $this->weatherCode;
    }

    /**
     * Set weather
     *
     * @param string $weather
     * @return Weather
     */
    public function setWeather($weather)
    {
        $this->weather = $weather;

        return $this;
    }

    /**
     * Get weather
     *
     * @return string 
     */
    public function getWeather()
    {
        return $this->weather;
    }

    /**
     * Set weatherDescription
     *
     * @param string $weatherDescription
     * @return Weather
     */
    public function setWeatherDescription($weatherDescription)
    {
        $this->weatherDescription = $weatherDescription;

        return $this;
    }

    /**
     * Get weatherDescription
     *
     * @return string 
     */
    public function getWeatherDescription()
    {
        return $this->weatherDescription;
    }

    /**
     * Set windSpeed
     *
     * @param float $windSpeed
     * @return Weather
     */
    public function setWindSpeed($windSpeed)
    {
        $this->windSpeed = $windSpeed;

        return $this;
    }

    /**
     * Get windSpeed
     *
     * @return float 
     */
    public function getWindSpeed()
    {
        return $this->windSpeed;
    }

    /**
     * Set windDeg
     *
     * @param integer $windDeg
     * @return Weather
     */
    public function setWindDeg($windDeg)
    {
        $this->windDeg = $windDeg;

        return $this;
    }

    /**
     * Get windDeg
     *
     * @return integer 
     */
    public function getWindDeg()
    {
        return $this->windDeg;
    }

    /**
     * Set clouds
     *
     * @param integer $clouds
     * @return Weather
     */
    public function setClouds($clouds)
    {
        $this->clouds = $clouds;

        return $this;
    }

    /**
     * Get clouds
     *
     * @return integer 
     */
    public function getClouds()
    {
        return $this->clouds;
    }

    /**
     * Set rain
     *
     * @param float $rain
     * @return Weather
     */
    public function setRain($rain)
    {
        $this->rain = $rain;

        return $this;
    }

    /**
     * Get rain
     *
     * @return float 
     */
    public function getRain()
    {
        return $this->rain;
    }

    /**
     * Set ride
     *
     * @param Ride $ride
     * @return Weather
     */
    public function setRide(Ride $ride = null)
    {
        $this->ride = $ride;

        return $this;
    }

    /**
     * Get ride
     *
     * @return Ride
     */
    public function getRide()
    {
        return $this->ride;
    }

    /**
     * Set weatherIcon
     *
     * @param string $weatherIcon
     * @return Weather
     */
    public function setWeatherIcon($weatherIcon)
    {
        $this->weatherIcon = $weatherIcon;

        return $this;
    }

    /**
     * Get weatherIcon
     *
     * @return string 
     */
    public function getWeatherIcon()
    {
        return $this->weatherIcon;
    }
}
