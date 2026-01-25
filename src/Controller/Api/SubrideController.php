<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Ride;
use App\Entity\Subride;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class SubrideController extends BaseController
{
    /**
     * Retrieve a list of subrides of a ride.
     *
     * Subrides are smaller events that happen within the context of a main ride.
     */
    #[Route(path: '/api/{citySlug}/{rideIdentifier}/subride', name: 'caldera_criticalmass_rest_subride_list', methods: ['GET'], priority: 190)]
    #[OA\Tag(name: 'Subride')]
    #[OA\Parameter(name: 'citySlug', in: 'path', description: 'Slug of the city', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'rideIdentifier', in: 'path', description: 'Identifier of the ride (date or slug)', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function listSubrideAction(Ride $ride): JsonResponse
    {
        $subrideList = $this->managerRegistry->getRepository(Subride::class)->findByRide($ride);

        return $this->createStandardResponse($subrideList);
    }

    /**
     * Show details of a specified subride.
     */
    #[Route(path: '/api/{citySlug}/{rideIdentifier}/{id}', name: 'caldera_criticalmass_rest_subride_show', requirements: ['id' => '\d+'], methods: ['GET'], priority: 190)]
    #[OA\Tag(name: 'Subride')]
    #[OA\Parameter(name: 'citySlug', in: 'path', description: 'Slug of the city', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'rideIdentifier', in: 'path', description: 'Identifier of the ride (date or slug)', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'id', in: 'path', description: 'Id of the subride', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function showSubrideAction(Subride $subride): JsonResponse
    {
        return $this->createStandardResponse($subride);
    }
}
