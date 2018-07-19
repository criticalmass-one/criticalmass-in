<?php

namespace App\Criticalmass\Weather;

use App\Entity\Ride;
use App\Entity\Weather;
use Cmfcmf\OpenWeatherMap;
use Cmfcmf\OpenWeatherMap\Forecast;
use Cmfcmf\OpenWeatherMap\WeatherForecast;
use Cmfcmf\OpenWeatherMap\Exception as OWMException;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

class WeatherForecastRetriever
{
    /** @var RegistryInterface $doctrine */
    protected $doctrine;

    /** @var OpenWeatherMap openWeatherMap */
    protected $openWeatherMap;

    /** @var array $newWeatherList */
    protected $newWeatherList = [];

    /** @var LoggerInterface $logger */
    protected $logger;

    public function __construct(RegistryInterface $doctrine, OpenWeatherMap $openWeatherMap, LoggerInterface $logger, string $openWeatherMapApiKey)
    {
        $this->doctrine = $doctrine;
        $this->logger = $logger;

        $this->openWeatherMap = $openWeatherMap;
        $this->openWeatherMap->setApiKey($openWeatherMapApiKey);
    }

    public function retrieve(\DateTime $startDateTime = null, \DateTime $endDateTime = null): array
    {
        if (!$startDateTime) {
            $startDateTime = new \DateTime();
        }

        if (!$endDateTime) {
            $endDateInterval = new \DateInterval('P1W');
            $endDateTime = new \DateTime();
            $endDateTime->add($endDateInterval);
        }

        $halfDayInterval = new \DateInterval('PT12H');
        $halfDateTime = new \DateTime();
        $halfDateTime->sub($halfDayInterval);

        $rideList = $this->findRides($startDateTime, $endDateTime);

        /** @var Ride $ride */
        foreach ($rideList as $ride) {
            $currentWeather = $this->findCurrentWeatherForRide($ride);

            if (!$currentWeather || $currentWeather->getCreationDateTime() < $halfDateTime) {
                $weather = $this->retrieveWeather($ride);

                if ($weather) {
                    $this->newWeatherList[] = $weather;

                    $this->logger->info(
                        sprintf(
                            'Loaded weather data for city %s and ride %s',
                            $ride->getCity()->getCity(),
                            $ride->getDateTime()->format('Y-m-d')
                        )
                    );
                }
            }
        }

        $this->doctrine->getManager()->flush();

        return $this->newWeatherList;
    }

    protected function retrieveWeather(Ride $ride): ?Weather
    {
        try {
            /** @var WeatherForecast $owmWeatherForecast */
            $owmWeatherForecast = $this->openWeatherMap->getWeatherForecast($this->getLatLng($ride), 'metric', 'de',
                null, 7);

            /** @var Forecast $owmWeather */
            while ($owmWeather = $owmWeatherForecast->current()) {
                if ($owmWeather->time->from->format('Y-m-d') == $ride->getDateTime()->format('Y-m-d')) {
                    break;
                }

                $owmWeatherForecast->next();
            }

            if ($owmWeather) {
                $weather = $this->createWeatherEntity($ride, $owmWeather);

                $this->doctrine->getManager()->persist($weather);

                return $weather;
            }
        } catch (OWMException $e) {
            $this->logger->alert(sprintf('Cannot retrieve weather data: %s (Code %s).', $e->getMessage(),
                $e->getCode()));
        } catch (\Exception $e) {
            $this->logger->alert(sprintf('Cannot retrieve weather data: %s (Code %s).', $e->getMessage(),
                $e->getCode()));
        }

        return null;
    }

    protected function createWeatherEntity(Ride $ride, Forecast $owmWeather): Weather
    {
        $weather = new Weather();

        $weather
            ->setRide($ride)
            ->setCreationDateTime(new \DateTime())
            ->setWeatherDateTime($owmWeather->time->from)
            ->setJson(null)
            ->setTemperatureMin($owmWeather->temperature->min->getValue())
            ->setTemperatureMax($owmWeather->temperature->max->getValue())
            ->setTemperatureMorning($owmWeather->temperature->morning->getValue())
            ->setTemperatureDay($owmWeather->temperature->day->getValue())
            ->setTemperatureEvening($owmWeather->temperature->evening->getValue())
            ->setTemperatureNight($owmWeather->temperature->night->getValue())
            ->setWeather(null)
            ->setWeatherDescription($owmWeather->weather->description)
            ->setWeatherCode($owmWeather->weather->id)
            ->setWeatherIcon($owmWeather->weather->icon)
            ->setPressure($owmWeather->pressure->getValue())
            ->setHumidity($owmWeather->humidity->getValue())
            ->setWindSpeed($owmWeather->wind->speed->getValue())
            ->setWindDeg($owmWeather->wind->direction->getValue())
            ->setClouds($owmWeather->clouds->getValue())
            ->setRain($owmWeather->precipitation->getValue());

        return $weather;
    }

    protected function getLatLng(Ride $ride): array
    {
        if ($ride->getHasLocation() && $ride->getCoord()) {
            $ride->getCoord()->toLatLonArray();
        }

        return $ride->getCity()->getCoord()->toLatLonArray();
    }

    protected function findRides(\DateTime $startDateTime, \DateTime $endDateTime): array
    {
        return $this->doctrine->getRepository(Ride::class)->findRidesInInterval($startDateTime, $endDateTime);
    }

    protected function findCurrentWeatherForRide(Ride $ride): ?Weather
    {
        return $this->doctrine->getRepository(Weather::class)->findCurrentWeatherForRide($ride);
    }

    public function getNewWeatherForecasts(): array
    {
        return $this->newWeatherList;
    }
}
