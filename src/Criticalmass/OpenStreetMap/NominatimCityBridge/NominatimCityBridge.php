<?php declare(strict_types=1);

namespace App\Criticalmass\OpenStreetMap\NominatimCityBridge;

use App\Entity\City;
use App\Entity\Region;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NominatimCityBridge extends AbstractNominatimCityBridge
{
    public function lookupCity(string $citySlug): ?City
    {
        $response = $this->httpClient->request('GET', self::NOMINATIM_URL . 'search', [
            'query' => [
                'city' => $citySlug,
                'format' => 'json',
                'addressdetails' => 1,
            ],
            'headers' => [
                'User-Agent' => 'criticalmass.in/1.0',
            ],
        ]);

        $result = $response->toArray();
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
            ->withTitle(sprintf('Critical Mass %s', $cityName))
            ->withRegion($region)
        ;

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
