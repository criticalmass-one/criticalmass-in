<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Criticalmass\DataQuery\DataQueryManager\DataQueryManagerInterface;
use App\Criticalmass\DataQuery\RequestParameterList\RequestToListConverter;
use App\Entity\City;
use App\Entity\Ride;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RideController extends BaseController
{
    /**
     * Retrieve information about a ride identified by <code>rideIdentifier</code> of a city identified by <code>citySlug</code>.
     *
     * As the parameter <code>citySlug</code> is just a string like <code>hamburg-harburg</code> or <code>muenchen</code> the parameter <code>rideIdentifier</code> is either the date of the ride like <code>2011-06-24</code> or a special identifier like <code>kidical-mass-hamburg-september-2019</code>.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns ride details",
     *  section="Ride",
     *  requirements={
     *    {"name"="citySlug", "dataType"="string", "required"=true, "description"="Provide the slug of a city."},
     *    {"name"="rideIdentifier", "dataType"="string", "required"=true, "description"="Provide the ride identifier of a ride."},
     *  }
     * )
     * @ParamConverter("ride", class="App:Ride")
     */
    public function showAction(Ride $ride): Response
    {
        $view = View::create();
        $view
            ->setData($ride)
            ->setFormat('json')
            ->setStatusCode(Response::HTTP_OK);

        return $this->handleView($view);
    }

    /**
     * Retrieve information about the current ride of a city identified by <code>citySlug</code>.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns details of the next ride in the city",
     *  section="Ride",
     *  requirements={
     *    {"name"="citySlug", "dataType"="string", "required"=true, "description"="Provide the slug of a city."},
     *  }
     * )
     * @ParamConverter("city", class="App:City")
     */
    public function showCurrentAction(Request $request, City $city, ManagerRegistry $registry): Response
    {
        $cycleMandatory = $request->query->getBoolean('cycleMandatory', false);
        $slugsAllowed = $request->query->getBoolean('slugsAllowed', true);
        
        $ride = $registry->getRepository(Ride::class)->findCurrentRideForCity($city, $cycleMandatory, $slugsAllowed);

        if (!$ride) {
            return new JsonResponse([], 200, []); // @todo this should return 404, but i have no clue how to handle multiple jquery requests then
        }

        $view = View::create();
        $view
            ->setData($ride)
            ->setFormat('json')
            ->setStatusCode(Response::HTTP_OK);

        return $this->handleView($view);
    }

    /**
     * Get a list of critical mass rides.
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
     * <li><code>regionSlug</code>: Provide a slug like <code>schleswig-holstein</code> to retrieve only rides from cities of this region.</li>
     * <li><code>citySlug</code>: Limit the resulting list to a city like <code>hamburg</code>, <code>new-york</code> or <code>muenchen</code>.</li>
     * </ul>
     *
     * <strong>Date-related query parameters</strong>
     *
     * <ul>
     * <li><code>year</code>: Retrieve only rides of the provided <code>year</code>.</li>
     * <li><code>month</code>: Retrieve only rides of the provided <code>year</code> and <code>month</code>. This will only work in combination with the previous <code>year</code> parameter.</li>
     * <li><code>day</code>: Limit the result list to a <code>day</code>. This parameter must be used with <code>year</code> and <code>month</code>.</li>
     * </ul>
     *
     * <strong>Geo query parameters</strong>
     *
     * <ul>
     * <li>Radius query: Specify <code>centerLatitude</code>, <code>centerLongitude</code> and a <code>radius</code> to retrieve all results within this circle.</li>
     * <li>Bounding Box query: Fetch all rides in the box described by <code>bbNorthLatitude</code>, <code>bbEastLongitude</code> and <code>bbSouthLatitude</code>, <code>bbWestLongitude</code>.
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
     * You may use the <code>distanceOrderDirection</code> parameter in combination with the radius query to sort the result list by the ride’s distance to the center coord.
     *
     * Apply <code>startValue</code> to deliver a value to start your ordered list with.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Lists rides",
     *  parameters={
     *     {"name"="regionSlug", "dataType"="string", "required"=false, "description"="Provide a region slug"},
     *     {"name"="citySlug", "dataType"="string", "required"=false, "description"="Provide a city slug"},
     *     {"name"="year", "dataType"="string", "required"=false, "description"="Limit the result set to this year. If not set, we will search in the current month."},
     *     {"name"="month", "dataType"="string", "required"=false, "description"="Limit the result set to this year. Must be combined with 'year'. If not set, we will search in the current month."},
     *     {"name"="day", "dataType"="string", "required"=false, "description"="Limit the result set to this day."},
     *     {"name"="centerLatitude", "dataType"="float", "required"=false, "description"="Latitude of a coordinate to search rides around in a given radius."},
     *     {"name"="centerLongitude", "dataType"="float", "required"=false, "description"="Longitude of a coordinate to search rides around in a given radius."},
     *     {"name"="radius", "dataType"="float", "required"=false, "description"="Radius to look around for rides."},
     *     {"name"="bbEastLongitude", "dataType"="float", "required"=false, "description"="East longitude of a bounding box to look for rides."},
     *     {"name"="bbWestLongitude", "dataType"="float", "required"=false, "description"="West longitude of a bounding box to look for rides."},
     *     {"name"="bbNorthLatitude", "dataType"="float", "required"=false, "description"="North latitude of a bounding box to look for rides."},
     *     {"name"="bbSouthLatitude", "dataType"="float", "required"=false, "description"="South latitude of a bounding box to look for rides."},
     *     {"name"="orderBy", "dataType"="string", "required"=false, "description"="Choose a property to sort the list by."},
     *     {"name"="orderDirection", "dataType"="string", "required"=false, "description"="Sort ascending or descending."},
     *     {"name"="distanceOrderDirection", "dataType"="string", "required"=false, "description"="Enable distance sorting in combination with radius query."},
     *     {"name"="startValue", "dataType"="string", "required"=false, "description"="Start ordered list with provided value."},
     *     {"name"="extended", "dataType"="boolean", "required"=false, "description"="Set true to retrieve a more detailed list."},
     *     {"name"="size", "dataType"="integer", "required"=false, "description"="Length of resulting list. Defaults to 10."}
     *  },
     *  section="Ride"
     * )
     */
    public function listAction(Request $request, DataQueryManagerInterface $dataQueryManager): Response
    {
        $queryParameterList = RequestToListConverter::convert($request);
        $rideList = $dataQueryManager->query($queryParameterList, Ride::class);

        $context = new Context();

        if ($request->query->has('extended') && true === $request->query->getBoolean('extended')) {
            $context->addGroup('extended-ride-list');
        }

        $context->addGroup('ride-list');

        $view = View::create();
        $view
            ->setData($rideList)
            ->setFormat('json')
            ->setStatusCode(Response::HTTP_OK)
            ->setContext($context);

        return $this->handleView($view);
    }
}
