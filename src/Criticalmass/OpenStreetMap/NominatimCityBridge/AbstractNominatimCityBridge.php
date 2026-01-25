<?php declare(strict_types=1);

namespace App\Criticalmass\OpenStreetMap\NominatimCityBridge;

use App\Factory\City\CityFactoryInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractNominatimCityBridge implements NominatimCityBridgeInterface
{
    const NOMINATIM_URL = 'https://nominatim.openstreetmap.org/';

    public function __construct(
        protected readonly ManagerRegistry $doctrine,
        protected readonly CityFactoryInterface $cityFactory,
        protected readonly HttpClientInterface $httpClient,
    ) {
    }
}
