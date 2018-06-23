<?php

namespace Criticalmass\Bundle\AppBundle\Controller\City;

use Criticalmass\Bundle\AppBundle\Controller\AbstractController;
use Criticalmass\Bundle\AppBundle\Criticalmass\OpenStreetMap\NominatimCityBridge\NominatimCityBridge;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MissingCityController extends AbstractController
{
    public function missingAction(Request $request, NominatimCityBridge $nominatimCityBridge, string $citySlug): Response
    {
        return $this->render('AppBundle:CityManagement:missing.html.twig', [
            'city' => $nominatimCityBridge->lookupCity($citySlug),
            'citySlug' => $citySlug,
        ]);
    }
}
