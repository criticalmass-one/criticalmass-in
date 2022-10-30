<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\City;
use App\Entity\Location;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class LocationController extends BaseController
{
    /**
     * @Operation(
     *     tags={"Location"},
     *     summary="Retrieve a list of locations of a city",
     *     @SWG\Parameter(
     *         name="citySlug",
     *         in="path",
     *         description="Slug of the city",
     *         required=true,
     *         @SWG\Schema(type="string"),
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @ParamConverter("city", class="App:City")
     * @Route("/{citySlug}/location", name="caldera_criticalmass_rest_location_list", methods={"GET"}, options={"expose"=true})
     */
    public function listLocationAction(ManagerRegistry $registry, City $city, SerializerInterface $serializer): JsonResponse
    {
        $locationList = $registry->getRepository(Location::class)->findLocationsByCity($city);

        return new JsonResponse($serializer->serialize($locationList, 'json'), JsonResponse::HTTP_OK, [], true);
    }

    /**
     * Show details of a specified location.
     *
     * @Operation(
     *     tags={"Location"},
     *     summary="Show details of a location",
     *     @SWG\Parameter(
     *         name="citySlug",
     *         in="path",
     *         description="Slug of the city",
     *         required=true,
     *         @SWG\Schema(type="string"),
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="locationSlug",
     *         in="path",
     *         description="Slug of the location",
     *         required=true,
     *         @SWG\Schema(type="string"),
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @ParamConverter("location", class="App:Location")
     * @Route("/{citySlug}/location/{locationSlug}", name="caldera_criticalmass_rest_location_show", methods={"GET"}, options={"expose"=true})
     */
    public function showLocationAction(Location $location, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse($serializer->serialize($location, 'json'), JsonResponse::HTTP_OK, [], true);
    }
}
