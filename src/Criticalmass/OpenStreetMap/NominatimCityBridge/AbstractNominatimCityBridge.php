<?php declare(strict_types=1);

namespace App\Criticalmass\OpenStreetMap\NominatimCityBridge;

use App\Factory\City\CityFactoryInterface;
use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractNominatimCityBridge implements NominatimCityBridgeInterface
{
    const NOMINATIM_URL = 'https://nominatim.openstreetmap.org/';

    /** @var ManagerRegistry $doctrine */
    protected $doctrine;

    /** @var CityFactoryInterface $cityFactory */
    protected $cityFactory;

    public function __construct(ManagerRegistry $doctrine, CityFactoryInterface $cityFactory)
    {
        $this->doctrine = $doctrine;
        $this->cityFactory = $cityFactory;
    }
}
