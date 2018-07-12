<?php

namespace App\Controller\City;

use App\Controller\AbstractController;
use App\Criticalmass\OpenStreetMap\NominatimCityBridge\NominatimCityBridge;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MissingCityController extends AbstractController
{
    public function missingAction(Request $request, NominatimCityBridge $nominatimCityBridge, string $citySlug): Response
    {
        return $this->render('App:CityManagement:missing.html.twig', [
            'city' => $nominatimCityBridge->lookupCity($citySlug),
            'citySlug' => $citySlug,
        ]);
    }
}
