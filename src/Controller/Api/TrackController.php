<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Criticalmass\DataQuery\DataQueryManager\DataQueryManagerInterface;
use App\Criticalmass\DataQuery\RequestParameterList\RequestToListConverter;
use App\Entity\Ride;
use App\Entity\Track;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @ParamConverter("ride", class="App:Ride")
     * @Route("/{citySlug}/{rideIdentifier}/listTracks", name="caldera_criticalmass_rest_track_ridelist", methods={"GET"})
     */
    public function listRideTrackAction(ManagerRegistry $registry, Ride $ride): Response
    {
        $trackList = $registry->getRepository(Track::class)->findByRide($ride);

        $view = View::create();
        $view
            ->setData($trackList)
            ->setFormat('json')
            ->setStatusCode(Response::HTTP_OK);

        return $this->handleView($view);
    }

    /**
     * Show details of a specified track.
     *
     * @Operation(
     *     tags={"Track"},
     *     summary="Show details of a track",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @ParamConverter("track", class="App:Track")
     * @Route("/track/{trackId}", name="caldera_criticalmass_rest_track_view", methods={"GET"})
     */
    public function viewAction(Track $track, UserInterface $user = null): Response
    {
        $context = new Context();

        $context->addGroup('api-public');

        if ($user) {
            $context->addGroup('api-private');
        }

        $view = View::create();
        $view
            ->setData($track)
            ->setFormat('json')
            ->setStatusCode(Response::HTTP_OK)
            ->setContext($context);

        return $this->handleView($view);
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
     *     @SWG\Parameter(
     *         name="regionSlug",
     *         in="body",
     *         description="Provide a region slug",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="citySlug",
     *         in="body",
     *         description="Provide a city slug",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="year",
     *         in="body",
     *         description="Limit the result set to this year. If not set, we will search in the current month.",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="month",
     *         in="body",
     *         description="Limit the result set to this year. Must be combined with 'year'. If not set, we will search in the current month.",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="day",
     *         in="body",
     *         description="Limit the result set to this day.",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="orderBy",
     *         in="body",
     *         description="Choose a property to sort the list by.",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="orderDirection",
     *         in="body",
     *         description="Sort ascending or descending.",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="startValue",
     *         in="body",
     *         description="Start ordered list with provided value.",
     *         required=false,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="size",
     *         in="body",
     *         description="Length of resulting list. Defaults to 10.",
     *         required=false,
     *         @SWG\Schema(type="integer")
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     * @Route("/track", name="caldera_criticalmass_rest_track_list", methods={"GET"})
     */
    public function listAction(Request $request, DataQueryManagerInterface $dataQueryManager, UserInterface $user = null): Response
    {
        $queryParameterList = RequestToListConverter::convert($request);
        $trackList = $dataQueryManager->query($queryParameterList, Track::class);

        $context = new Context();

        $context->addGroup('api-public');

        if ($user) {
            $context->addGroup('api-private');
        }

        $view = View::create();
        $view
            ->setData($trackList)
            ->setFormat('json')
            ->setStatusCode(Response::HTTP_OK)
            ->setContext($context);

        return $this->handleView($view);
    }
}
