<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Ride;
use App\Entity\Track;
use App\Event\Track\TrackDeletedEvent;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\SerializationContext;
use MalteHuebner\DataQueryBundle\DataQueryManager\DataQueryManagerInterface;
use MalteHuebner\DataQueryBundle\RequestParameterList\RequestToListConverter;
use OpenApi\Attributes as OA;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag(name: 'Track')]
class TrackController extends BaseController
{
    #[Route(path: '/{citySlug}/{rideIdentifier}/listTracks', name: 'caldera_criticalmass_rest_track_ridelist', methods: ['GET'])]
    #[OA\Get(
        path: '/{citySlug}/{rideIdentifier}/listTracks',
        summary: 'Retrieve a list of tracks of a ride',
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
                description: 'Ride identifier (date like 2011-06-24 or custom slug)',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Returned when successful'),
        ]
    )]
    public function listRideTrackAction(Ride $ride): JsonResponse
    {
        $trackList = $this->managerRegistry->getRepository(Track::class)->findByRide($ride);

        return $this->createStandardResponse($trackList);
    }

    #[Route(path: '/track/{id}', name: 'caldera_criticalmass_rest_track_view', methods: ['GET'])]
    #[OA\Get(
        path: '/track/{id}',
        summary: 'Show details of a track',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'Track ID',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Returned when successful'),
            new OA\Response(response: 404, description: 'Track not found'),
        ]
    )]
    public function viewAction(Track $track, ?UserInterface $user = null): JsonResponse
    {
        $groups = ['api-public'];
        if ($user) {
            $groups[] = 'api-private';
        }

        $context = new SerializationContext();
        $context->setGroups($groups);

        return $this->createStandardResponse($track, $context);
    }

    #[Route(path: '/track', name: 'caldera_criticalmass_rest_track_list', methods: ['GET'])]
    #[OA\Get(
        path: '/track',
        summary: 'Lists tracks',
        parameters: [
            new OA\Parameter(name: 'regionSlug', in: 'query', description: 'Provide a region slug', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'citySlug', in: 'query', description: 'Provide a city slug', required: false, schema: new OA\Schema(type: 'string')),

            new OA\Parameter(name: 'year', in: 'query', description: 'Year filter', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'month', in: 'query', description: 'Month filter (requires year)', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'day', in: 'query', description: 'Day filter (requires year and month)', required: false, schema: new OA\Schema(type: 'integer')),

            new OA\Parameter(name: 'orderBy', in: 'query', description: 'Property to sort by', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'orderDirection', in: 'query', description: 'asc or desc', required: false, schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'])),
            new OA\Parameter(name: 'startValue', in: 'query', description: 'Start value for ordered list', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'size', in: 'query', description: 'Length of resulting list (default 10)', required: false, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Returned when successful'),
        ]
    )]
    public function listAction(Request $request, DataQueryManagerInterface $dataQueryManager, ?UserInterface $user = null): JsonResponse
    {
        $queryParameterList = RequestToListConverter::convert($request);
        $trackList = $dataQueryManager->query($queryParameterList, Track::class);

        $groups = ['api-public'];
        if ($user) {
            $groups[] = 'api-private';
        }

        $context = new SerializationContext();
        $context->setGroups($groups);

        return $this->createStandardResponse($trackList, $context);
    }

    #[Route('/api/track/{id}', name: 'caldera_criticalmass_rest_track_delete', methods: ['DELETE'])]
    #[IsGranted(attribute: 'edit', subject: 'track')]
    #[OA\Delete(
        path: '/track/{id}',
        summary: 'Soft-delete a track (marks as deleted)',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'Track ID',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Deleted'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Track not found'),
        ]
    )]
    public function deleteAction(Track $track, EventDispatcherInterface $eventDispatcher, ManagerRegistry $managerRegistry): Response
    {
        $track->setDeleted(true);

        $managerRegistry->getManager()->flush();

        $eventDispatcher->dispatch(new TrackDeletedEvent($track), TrackDeletedEvent::NAME);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
