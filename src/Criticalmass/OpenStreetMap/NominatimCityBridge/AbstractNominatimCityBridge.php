<?php declare(strict_types=1);

namespace App\Criticalmass\OpenStreetMap\NominatimCityBridge;

use App\Factory\City\CityFactoryInterface;
use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractNominatimCityBridge implements NominatimCityBridgeInterface
{
    final const NOMINATIM_URL = 'https://nominatim.openstreetmap.org/';

    public function __construct(protected ManagerRegistry $doctrine, protected CityFactoryInterface $cityFactory)
    {
    }
}
