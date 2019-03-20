<?php declare(strict_types=1);

namespace App\Criticalmass\Weather\ForecastRetriever;

use App\Entity\Ride;
use App\Entity\Weather;
use Cmfcmf\OpenWeatherMap\Forecast;
use Cmfcmf\OpenWeatherMap\WeatherForecast;
use Cmfcmf\OpenWeatherMap\Exception as OWMException;

class WeatherForecastRetriever extends AbstractWeatherForecastRetriever
{
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
            if (!$ride->getLatitude() || !$ride->getLongitude()) {
                continue;
            }
            
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
                $weather = $this->createWeatherEntity($owmWeather);
                $weather->setRide($ride);

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
}
