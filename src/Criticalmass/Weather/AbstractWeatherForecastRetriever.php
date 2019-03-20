<?php declare(strict_types=1);

namespace App\Criticalmass\Weather;

use App\Entity\Ride;
use App\Entity\Weather;
use Cmfcmf\OpenWeatherMap;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

abstract class AbstractWeatherForecastRetriever
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
