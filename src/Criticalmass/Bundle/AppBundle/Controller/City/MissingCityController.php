<?php

namespace Criticalmass\Bundle\AppBundle\Controller\City;

use Criticalmass\Bundle\AppBundle\Controller\AbstractController;
use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Component\OpenStreetMap\NominatimCityBridge\NominatimCityBridge;
use maxh\Nominatim\Nominatim;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MissingCityController extends AbstractController
{
    public function missingAction(Request $request, string $citySlug): Response
    {
        $city = $this->get(NominatimCityBridge::class)->lookupCity($citySlug);

        return $this->render('AppBundle:CityManagement:missing.html.twig', [
            'city' => $city,
            'citySlug' => $citySlug,
        ]);
    }
}
