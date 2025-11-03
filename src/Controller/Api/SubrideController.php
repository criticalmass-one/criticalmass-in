<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Ride;
use App\Entity\Subride;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Subride')]
class SubrideController extends BaseController
{
    #[Route(
        path: '/api/{citySlug}/{rideIdentifier}/subride',
        name: 'caldera_criticalmass_rest_subride_list',
        methods: ['GET']
    )]
    #[OA\Get(
        path: '/api/{citySlug}/{rideIdentifier}/subride',
        summary: 'Retrieve a list of subrides of a ride',
        parameters: [
            new OA\Parameter(
                name: 'citySlug',
                in: 'path',
                description: 'Provide a city slug',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'rideIdentifier',
                in: 'path',
                description: 'Identify the requested ride (date like 2011-06-24 or custom slug)',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Returned when successful'),
        ]
    )]
    public function listSubrideAction(Ride $ride): JsonResponse
    {
        $subrideList = $this->managerRegistry
            ->getRepository(Subride::class)
            ->findByRide($ride);

        return $this->createStandardResponse($subrideList);
    }

    #[Route(
        path: '/api/{citySlug}/{rideIdentifier}/{id}',
        name: 'caldera_criticalmass_rest_subride_show',
        requirements: ['id' => '\d+'],
        methods: ['GET']
    )]
    #[OA\Get(
        path: '/api/{citySlug}/{rideIdentifier}/{id}',
        summary: 'Show details of a subride',
        parameters: [
            new OA\Parameter(
                name: 'citySlug',
                in: 'path',
                description: 'Provide a city slug',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'rideIdentifier',
                in: 'path',
                description: 'Identify the requested ride (date like 2011-06-24 or custom slug)',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'Numeric ID of the subride',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Returned when successful'),
            new OA\Response(response: 404, description: 'Subride not found'),
        ]
    )]
    public function showSubrideAction(Subride $subride): JsonResponse
    {
        return $this->createStandardResponse($subride);
    }
}
