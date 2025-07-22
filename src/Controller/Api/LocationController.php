<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\City;
use App\Entity\Location;
use Nelmio\ApiDocBundle\Annotation\Operation;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class LocationController extends BaseController
{
    /**
     * @Operation(
     *     tags={"Location"},
     *     summary="Retrieve a list of locations of a city",
     *     @OA\Parameter(
     *         name="citySlug",
     *         in="path",
     *         description="Slug of the city",
     *         required=true,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     */
    #[Route(path: '/{citySlug}/location', name: 'caldera_criticalmass_rest_location_list', methods: ['GET'])]
    public function listLocationAction(City $city): JsonResponse
    {
        $locationList = $this->managerRegistry->getRepository(Location::class)->findLocationsByCity($city);

        return $this->createStandardResponse($locationList);
    }

    /**
     * Show details of a specified location.
     *
     * @Operation(
     *     tags={"Location"},
     *     summary="Show details of a location",
     *     @OA\Parameter(
     *         name="citySlug",
     *         in="path",
     *         description="Slug of the city",
     *         required=true,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Parameter(
     *         name="locationSlug",
     *         in="path",
     *         description="Slug of the location",
     *         required=true,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     */
    #[Route(path: '/{citySlug}/location/{slug}', name: 'caldera_criticalmass_rest_location_show', methods: ['GET'])]
    public function showLocationAction(Location $location): JsonResponse
    {
        return $this->createStandardResponse($location);
    }
}
