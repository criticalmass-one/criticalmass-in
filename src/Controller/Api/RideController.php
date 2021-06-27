<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Criticalmass\DataQuery\DataQueryManager\DataQueryManagerInterface;
use App\Criticalmass\DataQuery\RequestParameterList\RequestToListConverter;
use App\Criticalmass\EntityMerger\EntityMergerInterface;
use App\Entity\City;
use App\Entity\Ride;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RideController extends BaseController
{
    /**
     * Retrieve information about a ride identified by <code>rideIdentifier</code> of a city identified by <code>citySlug</code>.
     *
     * As the parameter <code>citySlug</code> is just a string like <code>hamburg-harburg</code> or <code>muenchen</code> the parameter <code>rideIdentifier</code> is either the date of the ride like <code>2011-06-24</code> or a special identifier like <code>kidical-mass-hamburg-september-2019</code>.
     *
     * @Operation(
     *     tags={"Ride"},
     *     summary="Returns ride details",
     *     @SWG\Parameter(
     *         name="citySlug",
     *         in="path",
     *         description="Provide a city slug",
     *         required=true,
     *         type="string",
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="rideIdentifier",
     *         in="path",
     *         description="Identify the requested ride",
     *         required=true,
     *         type="string",
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @ParamConverter("ride", class="App:Ride")
     * @Route("/{citySlug}/{rideIdentifier}", name="caldera_criticalmass_rest_ride_show", methods={"GET"}, options={"expose"=true})
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
     * @Operation(
     *     tags={"Ride"},
     *     summary="Returns details of the next ride in the city",
     *     @SWG\Parameter(
     *         name="citySlug",
     *         in="path",
     *         description="Provide a city slug",
     *         required=true,
     *         type="string",
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @ParamConverter("city", class="App:City")
     * @Route("/{citySlug}/current", name="caldera_criticalmass_rest_ride_show_current", methods={"GET"}, options={"expose"=true})
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
     * <strong>Ride Type parameters</strong>
     *
     * For <code>rideType</code>, specify any of the following:
     *
     * <ul>
     * <li><code>critical_mass</code></li>
     * <li><code>kidical_mass</code></li>
     * <li><code>night_ride</code></li>
     * <li><code>lunch_ride</code></li>
     * <li><code>dawn_ride</code></li>
     * <li><code>dusk_ride</code></li>
     * <li><code>demonstration</code></li>
     * <li><code>alleycat</code></li>
     * <li><code>tour</code></li>
     * <li><code>event</code></li>
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
     * @Operation(
     *     tags={"Ride"},
     *     summary="Lists rides",
     *     @SWG\Parameter(
     *         name="regionSlug",
     *         in="query",
     *         description="Provide a region slug",
     *         required=false,
     *         @SWG\Schema(type="string"),
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="citySlug",
     *         in="query",
     *         description="Provide a city slug",
     *         required=false,
     *         @SWG\Schema(type="string"),
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="rideType",
     *         in="query",
     *         description="Limit to a type of events",
     *         required=false,
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="App\DBAL\Type\RideType")
     *         ),
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="year",
     *         in="query",
     *         description="Limit the result set to this year. If not set, we will search in the current month.",
     *         required=false,
     *         @SWG\Schema(type="integer"),
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="month",
     *         in="query",
     *         description="Limit the result set to this year. Must be combined with 'year'. If not set, we will search in the current month.",
     *         required=false,
     *         @SWG\Schema(type="integer"),
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="day",
     *         in="query",
     *         description="Limit the result set to this day.",
     *         required=false,
     *         @SWG\Schema(type="integer"),
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="centerLatitude",
     *         in="query",
     *         description="Latitude of a coordinate to search rides around in a given radius.",
     *         required=false,
     *         @SWG\Schema(type="float"),
     *         type="number"
     *     ),
     *     @SWG\Parameter(
     *         name="centerLongitude",
     *         in="query",
     *         description="Longitude of a coordinate to search rides around in a given radius.",
     *         required=false,
     *         @SWG\Schema(type="float"),
     *         type="number"
     *     ),
     *     @SWG\Parameter(
     *         name="radius",
     *         in="query",
     *         description="Radius to look around for rides.",
     *         required=false,
     *         @SWG\Schema(type="float"),
     *         type="number"
     *     ),
     *     @SWG\Parameter(
     *         name="bbEastLongitude",
     *         in="query",
     *         description="East longitude of a bounding box to look for rides.",
     *         required=false,
     *         @SWG\Schema(type="float"),
     *         type="number"
     *     ),
     *     @SWG\Parameter(
     *         name="bbWestLongitude",
     *         in="query",
     *         description="West longitude of a bounding box to look for rides.",
     *         required=false,
     *         @SWG\Schema(type="float"),
     *         type="number"
     *     ),
     *     @SWG\Parameter(
     *         name="bbNorthLatitude",
     *         in="query",
     *         description="North latitude of a bounding box to look for rides.",
     *         required=false,
     *         @SWG\Schema(type="float"),
     *         type="number"
     *     ),
     *     @SWG\Parameter(
     *         name="bbSouthLatitude",
     *         in="query",
     *         description="South latitude of a bounding box to look for rides.",
     *         required=false,
     *         @SWG\Schema(type="float"),
     *         type="number"
     *     ),
     *     @SWG\Parameter(
     *         name="orderBy",
     *         in="query",
     *         description="Choose a property to sort the list by.",
     *         required=false,
     *         @SWG\Schema(type="string"),
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="orderDirection",
     *         in="query",
     *         description="Sort ascending or descending.",
     *         required=false,
     *         @SWG\Schema(type="string"),
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="distanceOrderDirection",
     *         in="query",
     *         description="Enable distance sorting in combination with radius query.",
     *         required=false,
     *         @SWG\Schema(type="string"),
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="startValue",
     *         in="query",
     *         description="Start ordered list with provided value.",
     *         required=false,
     *         @SWG\Schema(type="string"),
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="extended",
     *         in="query",
     *         description="Set true to retrieve a more detailed list.",
     *         required=false,
     *         @SWG\Schema(type="boolean"),
     *         type="boolean"
     *     ),
     *     @SWG\Parameter(
     *         name="size",
     *         in="query",
     *         description="Length of resulting list. Defaults to 10.",
     *         required=false,
     *         @SWG\Schema(type="integer"),
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     * @Route("/ride", name="caldera_criticalmass_rest_ride_list", methods={"GET"})
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

    /**
     * @Operation(
     *     tags={"Ride"},
     *     summary="Creates a new ride",
     *     @SWG\Parameter(
     *         name="citySlug",
     *         in="path",
     *         description="Slug of the city to assign the new created ride to",
     *         required=true,
     *         type="string",
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="rideIdentifier",
     *         in="path",
     *         description="Identifier of the ride to be created",
     *         required=true,
     *         type="string",
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         description="JSON represantation of ride",
     *         required=true,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @ParamConverter("city", class="App:City")
     * @Route("/{citySlug}/{rideIdentifier}", name="caldera_criticalmass_rest_ride_create", methods={"PUT"})
     */
    public function createRideAction(Request $request, SerializerInterface $serializer, City $city, ManagerRegistry $managerRegistry, ValidatorInterface $validator): Response
    {
        /** @var Ride $ride */
        $ride = $this->deserializeRequest($request, $serializer, Ride::class);

        $ride->setCity($city);

        if (!$ride->getDateTime()) {
            $rideIdentifier = $request->get('rideIdentifier');

            try {
                $ride->setDateTime(new \DateTime($rideIdentifier));
            } catch (\Exception $exception) {
                if (!$ride->hasSlug()) {
                    $ride->setSlug($rideIdentifier);
                }
            }
        }

        $constraintViolationList = $validator->validate($ride);

        $errorList = [];

        /** @var ConstraintViolation $constraintViolation */
        foreach ($constraintViolationList as $constraintViolation) {
            $errorList[$constraintViolation->getPropertyPath()] = $constraintViolation->getMessage();
        }

        if (0 < count($errorList)) {
            return $this->createErrors(Response::HTTP_BAD_REQUEST, $errorList);
        }

        $manager = $managerRegistry->getManager();
        $manager->persist($ride);
        $manager->flush();

        $context = new Context();

        $context->addGroup('ride-list');

        $view = View::create();
        $view
            ->setData($ride)
            ->setFormat('json')
            ->setStatusCode(200)
            ->setContext($context);

        return $this->handleView($view);
    }

    /**
     * @Operation(
     *     tags={"Ride"},
     *     summary="Updates a ride",
     *     @SWG\Parameter(
     *         name="citySlug",
     *         in="path",
     *         description="Slug of the city to assign the updated ride to",
     *         required=true,
     *         type="string",
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="rideIdentifier",
     *         in="path",
     *         description="Identifier of the ride to be updated",
     *         required=true,
     *         type="string",
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         description="JSON represantation of ride",
     *         required=true,
     *         @SWG\Schema(type="string")
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @ParamConverter("ride", class="App:Ride")
     */
    public function updateRideAction(Request $request, Ride $ride, SerializerInterface $serializer, ManagerRegistry $managerRegistry, ValidatorInterface $validator, EntityMergerInterface $entityMerger): Response
    {
        /** @var Ride $ride */
        $updatedRide = $this->deserializeRequest($request, $serializer, Ride::class);

        $ride = $entityMerger->merge($updatedRide, $ride);

        if (!$ride->getDateTime()) {
            $rideIdentifier = $request->get('rideIdentifier');

            try {
                $ride->setDateTime(new \DateTime($rideIdentifier));
            } catch (\Exception $exception) {
                if (!$ride->hasSlug()) {
                    $ride->setSlug($rideIdentifier);
                }
            }
        }

        $constraintViolationList = $validator->validate($ride);

        $errorList = [];

        /** @var ConstraintViolation $constraintViolation */
        foreach ($constraintViolationList as $constraintViolation) {
            $errorList[$constraintViolation->getPropertyPath()] = $constraintViolation->getMessage();
        }

        if (0 < count($errorList)) {
            return $this->createErrors(Response::HTTP_BAD_REQUEST, $errorList);
        }

        $manager = $managerRegistry->getManager();
        $manager->flush();

        $context = new Context();

        $context->addGroup('ride-list');

        $view = View::create();
        $view
            ->setData($ride)
            ->setFormat('json')
            ->setStatusCode(200)
            ->setContext($context);

        return $this->handleView($view);
    }
}
