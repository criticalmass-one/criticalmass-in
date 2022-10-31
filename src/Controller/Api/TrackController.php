<?php declare(strict_types=1);

namespace App\Controller\Api;

use JMS\Serializer\SerializationContext;
use MalteHuebner\DataQueryBundle\DataQueryManager\DataQueryManagerInterface;
use MalteHuebner\DataQueryBundle\RequestParameterList\RequestToListConverter;
use App\Entity\Ride;
use App\Entity\Track;
use Nelmio\ApiDocBundle\Annotation\Operation;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class TrackController extends BaseController
{
    /**
     * Get a list of tracks which were uploaded to a specified ride.
     *
     * @Operation(
     *     tags={"Track"},
     *     summary="Retrieve a list of tracks of a ride",
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @ParamConverter("ride", class="App:Ride")
     * @Route("/{citySlug}/{rideIdentifier}/listTracks", name="caldera_criticalmass_rest_track_ridelist", methods={"GET"})
     */
    public function listRideTrackAction(Ride $ride): JsonResponse
    {
        $trackList = $this->managerRegistry->getRepository(Track::class)->findByRide($ride);

        return $this->createStandardResponse($trackList);
    }

    /**
     * Show details of a specified track.
     *
     * @Operation(
     *     tags={"Track"},
     *     summary="Show details of a track",
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @ParamConverter("track", class="App:Track")
     * @Route("/track/{trackId}", name="caldera_criticalmass_rest_track_view", methods={"GET"})
     */
    public function viewAction(Track $track, UserInterface $user = null): JsonResponse
    {
        $groups = ['api-public'];

        if ($user) {
            $groups[] = 'api-private';
        }

        $context = new SerializationContext();
        $context->setGroups($groups);

        return $this->createStandardResponse($track, $context);
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
     *
     * @Operation(
     *     tags={"Track"},
     *     summary="Lists tracks",
     *     @OA\Parameter(
     *         name="regionSlug",
     *         in="query",
     *         description="Provide a region slug",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="citySlug",
     *         in="query",
     *         description="Provide a city slug",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="year",
     *         in="query",
     *         description="Limit the result set to this year. If not set, we will search in the current month.",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="month",
     *         in="query",
     *         description="Limit the result set to this year. Must be combined with 'year'. If not set, we will search in the current month.",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="day",
     *         in="query",
     *         description="Limit the result set to this day.",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="orderBy",
     *         in="query",
     *         description="Choose a property to sort the list by.",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="orderDirection",
     *         in="query",
     *         description="Sort ascending or descending.",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="startValue",
     *         in="query",
     *         description="Start ordered list with provided value.",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="size",
     *         in="query",
     *         description="Length of resulting list. Defaults to 10.",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     * @Route("/track", name="caldera_criticalmass_rest_track_list", methods={"GET"})
     */
    public function listAction(Request $request, DataQueryManagerInterface $dataQueryManager,UserInterface $user = null): JsonResponse
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
}
