<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Criticalmass\DataQuery\DataQueryManager\DataQueryManagerInterface;
use App\Criticalmass\DataQuery\RequestParameterList\RequestToListConverter;
use App\Criticalmass\EntityMerger\EntityMergerInterface;
use App\Entity\City;
use App\Entity\Ride;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RideController extends BaseController
{
    /**
     * Retrieve information about a ride identified by <code>rideIdentifier</code> of a city identified by <code>citySlug</code>.
     *
     * As the parameter <code>citySlug</code> is just a string like <code>hamburg-harburg</code> or <code>muenchen</code> the parameter <code>rideIdentifier</code> is either the date of the ride like <code>2011-06-24</code> or a special identifier like <code>kidical-mass-hamburg-september-2019</code>.
     */
    #[Route(path: '/api/{citySlug}/{rideIdentifier}', name: 'caldera_criticalmass_rest_ride_show', methods: ['GET'], priority: 170)]
    #[OA\Tag(name: 'Ride')]
    #[OA\Parameter(name: 'citySlug', in: 'path', description: 'Provide a city slug', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'rideIdentifier', in: 'path', description: 'Identify the requested ride', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function showAction(Ride $ride): JsonResponse
    {
        $context = ['groups' => 'ride-details'];

        return $this->createStandardResponse($ride, $context);
    }

    /**
     * Retrieve information about the current ride of a city identified by <code>citySlug</code>.
     */
    #[Route(path: '/api/{citySlug}/current', name: 'caldera_criticalmass_rest_ride_show_current', methods: ['GET'], priority: 190)]
    #[OA\Tag(name: 'Ride')]
    #[OA\Parameter(name: 'citySlug', in: 'path', description: 'Provide a city slug', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function showCurrentAction(Request $request, City $city): JsonResponse
    {
        $cycleMandatory = $request->query->getBoolean('cycleMandatory', false);
        $slugsAllowed = $request->query->getBoolean('slugsAllowed', true);

        $ride = $this->managerRegistry->getRepository(Ride::class)->findCurrentRideForCity($city, $cycleMandatory, $slugsAllowed);

        if (!$ride) {
            return new JsonResponse([], JsonResponse::HTTP_OK, []);
        }

        return $this->createStandardResponse($ride);
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
     * <li><code>dateTime</code></li>
     * </ul>
     *
     * Specify the order direction with <code>orderDirection=asc</code> or <code>orderDirection=desc</code>.
     *
     * You may use the <code>distanceOrderDirection</code> parameter in combination with the radius query to sort the result list by the ride's distance to the center coord.
     *
     * Apply <code>startValue</code> to deliver a value to start your ordered list with.
     */
    #[Route(path: '/api/ride', name: 'caldera_criticalmass_rest_ride_list', methods: ['GET'], priority: 200)]
    #[OA\Tag(name: 'Ride')]
    #[OA\Parameter(name: 'regionSlug', in: 'query', description: 'Provide a region slug', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'citySlug', in: 'query', description: 'Provide a city slug', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'rideType', in: 'query', description: 'Limit to a type of events', schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'string')))]
    #[OA\Parameter(name: 'year', in: 'query', description: 'Limit the result set to this year.', schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'month', in: 'query', description: 'Limit the result set to this month. Must be combined with year.', schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'day', in: 'query', description: 'Limit the result set to this day.', schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'centerLatitude', in: 'query', description: 'Latitude of a coordinate to search rides around in a given radius.', schema: new OA\Schema(type: 'number'))]
    #[OA\Parameter(name: 'centerLongitude', in: 'query', description: 'Longitude of a coordinate to search rides around in a given radius.', schema: new OA\Schema(type: 'number'))]
    #[OA\Parameter(name: 'radius', in: 'query', description: 'Radius to look around for rides.', schema: new OA\Schema(type: 'number'))]
    #[OA\Parameter(name: 'bbEastLongitude', in: 'query', description: 'East longitude of a bounding box to look for rides.', schema: new OA\Schema(type: 'number'))]
    #[OA\Parameter(name: 'bbWestLongitude', in: 'query', description: 'West longitude of a bounding box to look for rides.', schema: new OA\Schema(type: 'number'))]
    #[OA\Parameter(name: 'bbNorthLatitude', in: 'query', description: 'North latitude of a bounding box to look for rides.', schema: new OA\Schema(type: 'number'))]
    #[OA\Parameter(name: 'bbSouthLatitude', in: 'query', description: 'South latitude of a bounding box to look for rides.', schema: new OA\Schema(type: 'number'))]
    #[OA\Parameter(name: 'orderBy', in: 'query', description: 'Choose a property to sort the list by.', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'orderDirection', in: 'query', description: 'Sort ascending or descending.', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'distanceOrderDirection', in: 'query', description: 'Enable distance sorting in combination with radius query.', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'startValue', in: 'query', description: 'Start ordered list with provided value.', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'extended', in: 'query', description: 'Set true to retrieve a more detailed list.', schema: new OA\Schema(type: 'boolean'))]
    #[OA\Parameter(name: 'size', in: 'query', description: 'Length of resulting list. Defaults to 10.', schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function listAction(Request $request, DataQueryManagerInterface $dataQueryManager): JsonResponse
    {
        $queryParameterList = RequestToListConverter::convert($request);
        $rideList = $dataQueryManager->query($queryParameterList, Ride::class);

        $groups = ['ride-list'];

        if ($request->query->has('extended') && true === $request->query->getBoolean('extended')) {
            $groups[] = 'extended-ride-list';
        }

        return $this->createStandardResponse($rideList, ['groups' => $groups]);
    }

    /**
     * Creates a new ride.
     */
    #[Route(path: '/api/{citySlug}/{rideIdentifier}', name: 'caldera_criticalmass_rest_ride_create', methods: ['PUT'], priority: 170)]
    #[OA\Tag(name: 'Ride')]
    #[OA\Parameter(name: 'citySlug', in: 'path', description: 'Slug of the city to assign the new created ride to', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'rideIdentifier', in: 'path', description: 'Identifier of the ride to be created', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\RequestBody(description: 'JSON representation of ride', required: true, content: new OA\JsonContent(type: 'object'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function createRideAction(Request $request, City $city, ValidatorInterface $validator): JsonResponse
    {
        /** @var Ride $ride */
        $ride = $this->deserializeRequest($request, Ride::class);

        $ride->setCity($city);

        $rideIdentifier = $request->get('rideIdentifier');
        $isDateIdentifier = false;

        try {
            $dateTime = new \DateTime($rideIdentifier);
            $isDateIdentifier = true;

            if (!$ride->getDateTime()) {
                $ride->setDateTime($dateTime);
            }
        } catch (\Exception) {
            if (!$ride->hasSlug()) {
                $ride->setSlug($rideIdentifier);
            }
        }

        $constraintViolationList = $validator->validate($ride);

        $errorList = [];

        /** @var ConstraintViolation $constraintViolation */
        foreach ($constraintViolationList as $constraintViolation) {
            $errorList[$constraintViolation->getPropertyPath()] = $constraintViolation->getMessage();
        }

        if (0 < count($errorList)) {
            return $this->createErrors(JsonResponse::HTTP_BAD_REQUEST, $errorList);
        }

        $manager = $this->managerRegistry->getManager();
        $manager->persist($ride);
        $manager->flush();

        return $this->createStandardResponse($ride, ['groups' => ['ride-list']]);
    }

    /**
     * Updates a ride.
     */
    #[Route(path: '/api/{citySlug}/{rideIdentifier}', name: 'caldera_criticalmass_rest_ride_update', methods: ['POST'], priority: 170)]
    #[OA\Tag(name: 'Ride')]
    #[OA\Parameter(name: 'citySlug', in: 'path', description: 'Slug of the city to assign the updated ride to', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'rideIdentifier', in: 'path', description: 'Identifier of the ride to be updated', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\RequestBody(description: 'JSON representation of ride', required: true, content: new OA\JsonContent(type: 'object'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function updateRideAction(Request $request, Ride $ride, ValidatorInterface $validator, EntityMergerInterface $entityMerger): JsonResponse
    {
        /** @var Ride $ride */
        $updatedRide = $this->deserializeRequest($request, Ride::class);

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
            return $this->createErrors(JsonResponse::HTTP_BAD_REQUEST, $errorList);
        }

        $manager = $this->managerRegistry->getManager();
        $manager->flush();

        return $this->createStandardResponse($ride, ['groups' => ['ride-list']]);
    }
}
