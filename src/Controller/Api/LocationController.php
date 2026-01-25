<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\City;
use App\Entity\Location;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class LocationController extends BaseController
{
    /**
     * Retrieve a list of locations of a city.
     */
    #[Route(path: '/api/{citySlug}/location', name: 'caldera_criticalmass_rest_location_list', methods: ['GET'], priority: 190)]
    #[OA\Tag(name: 'Location')]
    #[OA\Parameter(name: 'citySlug', in: 'path', description: 'Slug of the city', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function listLocationAction(City $city): JsonResponse
    {
        $locationList = $this->managerRegistry->getRepository(Location::class)->findLocationsByCity($city);

        return $this->createStandardResponse($locationList);
    }

    /**
     * Show details of a specified location.
     */
    #[Route(path: '/api/{citySlug}/location/{slug}', name: 'caldera_criticalmass_rest_location_show', methods: ['GET'], priority: 190)]
    #[OA\Tag(name: 'Location')]
    #[OA\Parameter(name: 'citySlug', in: 'path', description: 'Slug of the city', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'locationSlug', in: 'path', description: 'Slug of the location', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function showLocationAction(Location $location): JsonResponse
    {
        return $this->createStandardResponse($location);
    }
}
