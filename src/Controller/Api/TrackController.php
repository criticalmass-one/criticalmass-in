<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Criticalmass\DataQuery\DataQueryManager\DataQueryManagerInterface;
use App\Criticalmass\DataQuery\RequestParameterList\RequestToListConverter;
use App\Entity\Ride;
use App\Entity\Track;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
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
     * @ApiDoc(
     *  resource=true,
     *  description="Retrieve a list of tracks of a ride",
     *  section="Track",
     *  requirements={
     *    {"name"="citySlug", "dataType"="string", "required"=true, "description"="Provide the slug of a city."},
     *    {"name"="rideIdentifier", "dataType"="string", "required"=true, "description"="Provide the ride identifier of a ride."},
     *  }
     * )
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
            ->setStatusCode(200);

        return $this->handleView($view);
    }

    /**
     * Show details of a specified track.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Show details of a track",
     *  section="Track",
     *  requirements={
     *    {"name"="trackId", "dataType"="int", "required"=true, "description"="Unique id of the track."}
     *  }
     * )
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
            ->setStatusCode(200)
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
     * @ApiDoc(
     *  resource=true,
     *  description="Lists tracks",
     *  parameters={
     *     {"name"="regionSlug", "dataType"="string", "required"=false, "description"="Provide a region slug"},
     *     {"name"="citySlug", "dataType"="string", "required"=false, "description"="Provide a city slug"},
     *     {"name"="year", "dataType"="string", "required"=false, "description"="Limit the result set to this year. If not set, we will search in the current month."},
     *     {"name"="month", "dataType"="string", "required"=false, "description"="Limit the result set to this year. Must be combined with 'year'. If not set, we will search in the current month."},
     *     {"name"="day", "dataType"="string", "required"=false, "description"="Limit the result set to this day."},
     *     {"name"="orderBy", "dataType"="string", "required"=false, "description"="Choose a property to sort the list by."},
     *     {"name"="orderDirection", "dataType"="string", "required"=false, "description"="Sort ascending or descending."},
     *     {"name"="startValue", "dataType"="string", "required"=false, "description"="Start ordered list with provided value."},
     *     {"name"="size", "dataType"="integer", "required"=false, "description"="Length of resulting list. Defaults to 10."}
     *  },
     *  section="Track"
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
            ->setStatusCode(200)
            ->setContext($context);

        return $this->handleView($view);
    }
}
