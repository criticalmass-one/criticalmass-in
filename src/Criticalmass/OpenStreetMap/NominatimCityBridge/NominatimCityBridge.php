<?php declare(strict_types=1);

namespace App\Criticalmass\OpenStreetMap\NominatimCityBridge;

use App\Entity\City;
use App\Entity\Region;
use maxh\Nominatim\Nominatim;

class NominatimCityBridge extends AbstractNominatimCityBridge
{
    public function lookupCity(string $citySlug): ?City
    {
        $nominatim = new Nominatim(self::NOMINATIM_URL);

        $search = $nominatim->newSearch()
            ->city($citySlug)
            ->addressDetails();

        $result = $nominatim->find($search);
        $firstResult = array_shift($result);

        if ($firstResult) {
            $city = $this->createCity($firstResult);

            return $city;
        }

        return null;
    }

    protected function createCity(array $result): ?City
    {
        $region = $this->doctrine->getRepository(Region::class)->findOneByName($result['address']['state']);

        $cityName = $this->getCityNameFromResult($result);

        if (!$region || !$cityName) {
            return null;
        }

        $this->cityFactory
            ->withLatitude((float) $result['lat'])
            ->withLongitude((float) $result['lon'])
            ->withName($cityName)
            ->withRegion($region);

        return $this->cityFactory->build();
    }

    protected function getCityNameFromResult(array $result): ?string
    {
        $propertyOrder = ['city', 'town', 'village', 'suburb'];

        foreach ($propertyOrder as $property) {
            if (array_key_exists($property, $result['address'])) {
                return $result['address'][$property];
            }
        }

        return null;
    }
}
