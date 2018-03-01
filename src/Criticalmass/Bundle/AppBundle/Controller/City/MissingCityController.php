<?php

namespace Criticalmass\Bundle\AppBundle\Controller\City;

use Criticalmass\Bundle\AppBundle\Controller\AbstractController;
use Criticalmass\Bundle\AppBundle\Entity\City;
use maxh\Nominatim\Nominatim;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MissingCityController extends AbstractController
{
    public function missingAction(Request $request, string $citySlug): Response
    {
        $url = 'https://nominatim.openstreetmap.org/';
        $nominatim = new Nominatim($url);

        $search = $nominatim->newSearch()
            ->city($citySlug)
            ->addressDetails();

        $result = $nominatim->find($search);
        $firstResult = array_shift($result);

        if ($firstResult) {
            $city = $this->lookupCity($citySlug);

            return $this->render('AppBundle:CityManagement:missing.html.twig', [
                'city' => $city,
            ]);
        }

        throw $this->createNotFoundException();
    }

    protected function lookupCity(string $citySlug): ?City
    {
        $url = 'https://nominatim.openstreetmap.org/';
        $nominatim = new Nominatim($url);

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

    protected function createCity(array $result): City
    {
        $city = new City();

        $city
            ->setLatitude($result['lat'])
            ->setLongitude($result['lon'])
            ->setCity($result['address']['city'])
        ;

        return $city;
    }
}
