<?php declare(strict_types=1);

namespace App\Controller\Api;

use MalteHuebner\DataQueryBundle\DataQueryManager\DataQueryManagerInterface;
use MalteHuebner\DataQueryBundle\RequestParameterList\RequestToListConverter;
use App\Entity\Ride;
use App\Entity\Track;
use App\Event\Track\TrackDeletedEvent;
use Doctrine\Persistence\ManagerRegistry;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TrackController extends BaseController
{
    /**
     * Get a list of tracks which were uploaded to a specified ride.
     */
    #[Route(path: '/api/{citySlug}/{rideIdentifier}/listTracks', name: 'caldera_criticalmass_rest_track_ridelist', methods: ['GET'])]
    #[OA\Tag(name: 'Track')]
    #[OA\Parameter(name: 'citySlug', in: 'path', description: 'Slug of the city', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'rideIdentifier', in: 'path', description: 'Identifier of the ride (date or slug)', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function listRideTrackAction(Ride $ride): JsonResponse
    {
        $trackList = $this->managerRegistry->getRepository(Track::class)->findByRide($ride);

        return $this->createStandardResponse($trackList);
    }

    /**
     * Show details of a specified track.
     */
    #[Route(path: '/api/track/{trackId}', name: 'caldera_criticalmass_rest_track_view', methods: ['GET'])]
    #[OA\Tag(name: 'Track')]
    #[OA\Parameter(name: 'trackId', in: 'path', description: 'Id of the track', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function viewAction(Track $track, ?UserInterface $user = null): JsonResponse
    {
        $groups = ['api-public'];

        if ($user) {
            $groups[] = 'api-private';
        }

        return $this->createStandardResponse($track);
    }

    /**
     * Get a list of uploaded tracks.
     *
     * You may specify your query with the following parameters.
     *
     * <strong>List length</strong>
     *
     * The length of your results defaults to 10. Use <code>size</code> to request more or less results.
     *
     * <strong>Regional query parameters</strong>
     *
     * <ul>
     * <li><code>regionSlug</code>: Provide a slug like <code>schleswig-holstein</code> to retrieve only tracks from cities of this region.</li>
     * <li><code>citySlug</code>: Limit the resulting list to a city like <code>hamburg</code>, <code>new-york</code> or <code>muenchen</code>.</li>
     * </ul>
     *
     * <strong>Date-related query parameters</strong>
     *
     * <ul>
     * <li><code>year</code>: Retrieve only tracks of the provided <code>year</code>.</li>
     * <li><code>month</code>: Retrieve only tracks of the provided <code>year</code> and <code>month</code>. This will only work in combination with the previous <code>year</code> parameter.</li>
     * <li><code>day</code>: Limit the result list to a <code>day</code>. This parameter must be used with <code>year</code> and <code>month</code>.</li>
     * </ul>
     *
     * <strong>Order parameters</strong>
     *
     * Sort the resulting list with the parameter <code>orderBy</code> and choose from one of the following properties:
     *
     * <ul>
     * <li><code>id</code></li>
     * <li><code>slug</code></li>
     * <li><code>title</code></li>
     * <li><code>description</code></li>
     * <li><code>socialDescription</code></li>
     * <li><code>latitude</code></li>
     * <li><code>longitude</code></li>
     * <li><code>estimatedParticipants</code></li>
     * <li><code>estimatedDuration</code></li>
     * <li><code>estimatedDistance</code></li>
     * <li><code>views</code></li>
     * <li><code>dateTime</code></li>
     * </ul>
     *
     * Specify the order direction with <code>orderDirection=asc</code> or <code>orderDirection=desc</code>.
     *
     * Apply <code>startValue</code> to deliver a value to start your ordered list with.
     */
    #[Route(path: '/api/track', name: 'caldera_criticalmass_rest_track_list', methods: ['GET'])]
    #[OA\Tag(name: 'Track')]
    #[OA\Parameter(name: 'regionSlug', in: 'query', description: 'Provide a region slug', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'citySlug', in: 'query', description: 'Provide a city slug', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'year', in: 'query', description: 'Limit the result set to this year.', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'month', in: 'query', description: 'Limit the result set to this month. Must be combined with year.', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'day', in: 'query', description: 'Limit the result set to this day.', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'orderBy', in: 'query', description: 'Choose a property to sort the list by.', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'orderDirection', in: 'query', description: 'Sort ascending or descending.', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'startValue', in: 'query', description: 'Start ordered list with provided value.', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'size', in: 'query', description: 'Length of resulting list. Defaults to 10.', schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function listAction(Request $request, DataQueryManagerInterface $dataQueryManager, ?UserInterface $user = null): JsonResponse
    {
        $queryParameterList = RequestToListConverter::convert($request);
        $trackList = $dataQueryManager->query($queryParameterList, Track::class);

        $groups = ['api-public'];

        if ($user) {
            $groups[] = 'api-private';
        }

        return $this->createStandardResponse($trackList);
    }

    /**
     * Delete a track.
     *
     * Marks the track as deleted. Requires edit permissions on the track.
     */
    #[Route('/api/track/{id}', name: 'caldera_criticalmass_rest_track_delete', methods: ['DELETE'])]
    #[IsGranted('edit', 'track')]
    #[OA\Tag(name: 'Track')]
    #[OA\Parameter(name: 'id', in: 'path', description: 'Id of the track to delete', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 302, description: 'Redirects to track list on success')]
    #[OA\Response(response: 403, description: 'Returned when user lacks edit permissions')]
    public function deleteAction(Track $track, EventDispatcherInterface $eventDispatcher, ManagerRegistry $managerRegistry): Response
    {
        $track->setDeleted(true);

        $managerRegistry->getManager()->flush();

        $eventDispatcher->dispatch(new TrackDeletedEvent($track), TrackDeletedEvent::NAME);

        return $this->redirectToRoute('caldera_criticalmass_track_list');
    }
}
