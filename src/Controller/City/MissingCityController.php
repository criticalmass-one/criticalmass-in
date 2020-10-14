<?php declare(strict_types=1);

namespace App\Controller\City;

use App\Controller\AbstractController;
use App\Criticalmass\OpenStreetMap\NominatimCityBridge\NominatimCityBridgeInterface;
use Symfony\Component\HttpFoundation\Response;

class MissingCityController extends AbstractController
{
    public function missingAction(NominatimCityBridgeInterface $nominatimCityBridge, string $citySlug): Response
    {
        return $this->render('CityManagement/missing.html.twig', [
            'city' => $nominatimCityBridge->lookupCity($citySlug),
            'citySlug' => $citySlug,
        ]);
    }
}
