<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\City;
use App\Entity\Location;
use Doctrine\Persistence\ManagerRegistry;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Location')]
class LocationController extends BaseController
{
    #[Route(path: '/api/{citySlug}/location', name: 'caldera_criticalmass_rest_location_list', methods: ['GET'])]
    #[OA\Get(
        path: '/api/{citySlug}/location',
        summary: 'Retrieve a list of locations of a city',
        parameters: [
            new OA\Parameter(
                name: 'citySlug',
                in: 'path',
                description: 'Slug of the city',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Returned when successful'),
        ]
    )]
    public function listLocationAction(ManagerRegistry $registry, City $city): JsonResponse
    {
        $locationList = $registry->getRepository(Location::class)->findLocationsByCity($city);

        return $this->createStandardResponse($locationList);
    }

    #[Route(path: '/api/{citySlug}/location/{slug}', name: 'caldera_criticalmass_rest_location_show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/{citySlug}/location/{slug}',
        summary: 'Show details of a location',
        parameters: [
            new OA\Parameter(
                name: 'citySlug',
                in: 'path',
                description: 'Slug of the city',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'slug',
                in: 'path',
                description: 'Slug of the location',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Returned when successful'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function showLocationAction(Location $location): JsonResponse
    {
        return $this->createStandardResponse($location);
    }
}
