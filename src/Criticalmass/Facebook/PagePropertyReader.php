<?php declare(strict_types=1);

namespace App\Criticalmass\Facebook;

use App\Entity\City;
use App\Criticalmass\Facebook\Api\FacebookPageApi;
use App\Criticalmass\Facebook\Bridge\CityBridge;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

class PagePropertyReader
{
    /** @var Doctrine $doctrine */
    protected $doctrine;

    /** @var CityBridge $cityBridge */
    protected $cityBridge;

    /** @var array $readCities */
    protected $propertyList = [];

    public function __construct(Doctrine $doctrine, CityBridge $cityBridge)
    {
        $this->doctrine = $doctrine;
        $this->cityBridge = $cityBridge;
    }

    public function read(): PagePropertyReader
    {
        $cities = $this->doctrine->getRepository(City::class)->findCitiesWithFacebook();

        /** @var City $city */
        foreach ($cities as $city) {
            $properties = $this->cityBridge->getPagePropertiesForCity($city);

            if ($properties) {
                $this->doctrine->getManager()->persist($properties);

                $this->propertyList[] = $properties;
            }
        }

        $this->doctrine->getManager()->flush();

        return $this;
    }

    public function getPropertyList(): array
    {
        return $this->propertyList;
    }
}
