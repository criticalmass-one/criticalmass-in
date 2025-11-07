<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Criticalmass\EntityMerger\EntityMergerInterface;
use App\Entity\City;
use App\Entity\Ride;
use JMS\Serializer\SerializationContext;
use MalteHuebner\DataQueryBundle\DataQueryManager\DataQueryManagerInterface;
use MalteHuebner\DataQueryBundle\RequestParameterList\RequestToListConverter;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Tag(name: 'Ride')]
class RideController extends BaseController
{
    #[Route(path: '/{citySlug}/{rideIdentifier}', name: 'caldera_criticalmass_rest_ride_show', methods: ['GET'])]
    #[OA\Get(
        path: '/{citySlug}/{rideIdentifier}',
        summary: 'Returns ride details',
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
            new OA\Response(response: 404, description: 'Ride not found'),
        ]
    )]
    public function showAction(Ride $ride): JsonResponse
    {
        return $this->createStandardResponse($ride);
    }

    #[Route(path: '/{citySlug}/current', name: 'caldera_criticalmass_rest_ride_show_current', methods: ['GET'])]
    #[OA\Get(
        path: '/{citySlug}/current',
        summary: 'Returns details of the next ride in the city',
        parameters: [
            new OA\Parameter(
                name: 'citySlug',
                in: 'path',
                description: 'Provide a city slug',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'cycleMandatory',
                in: 'query',
                description: 'Require cycle to be mandatory',
                required: false,
                schema: new OA\Schema(type: 'boolean')
            ),
            new OA\Parameter(
                name: 'slugsAllowed',
                in: 'query',
                description: 'Allow slug-based identification',
                required: false,
                schema: new OA\Schema(type: 'boolean')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Returned when successful (empty array if none)'),
        ]
    )]
    public function showCurrentAction(Request $request, City $city): JsonResponse
    {
        $cycleMandatory = $request->query->getBoolean('cycleMandatory', false);
        $slugsAllowed = $request->query->getBoolean('slugsAllowed', true);

        $ride = $this->managerRegistry
            ->getRepository(Ride::class)
            ->findCurrentRideForCity($city, $cycleMandatory, $slugsAllowed);

        if (!$ride) {
            return new JsonResponse([], JsonResponse::HTTP_OK, []);
        }

        return $this->createStandardResponse($ride);
    }

    #[Route(path: '/ride', name: 'caldera_criticalmass_rest_ride_list', methods: ['GET'])]
    #[OA\Get(
        path: '/ride',
        summary: 'Lists rides',
        parameters: [
            new OA\Parameter(name: 'regionSlug', in: 'query', description: 'Provide a region slug', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'citySlug', in: 'query', description: 'Provide a city slug', required: false, schema: new OA\Schema(type: 'string')),

            new OA\Parameter(
                name: 'rideType',
                in: 'query',
                description: 'Limit to a type of events',
                required: false,
                schema: new OA\Schema(
                    type: 'array',
                    items: new OA\Items(type: 'string', enum: [
                        'critical_mass', 'kidical_mass', 'night_ride', 'lunch_ride',
                        'dawn_ride', 'dusk_ride', 'demonstration', 'alleycat', 'tour', 'event',
                    ])
                )
            ),

            new OA\Parameter(name: 'year', in: 'query', description: 'Year filter', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'month', in: 'query', description: 'Month filter (requires year)', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'day', in: 'query', description: 'Day filter (requires year and month)', required: false, schema: new OA\Schema(type: 'integer')),

            new OA\Parameter(name: 'centerLatitude', in: 'query', description: 'Center latitude for radius query', required: false, schema: new OA\Schema(type: 'number', format: 'float')),
            new OA\Parameter(name: 'centerLongitude', in: 'query', description: 'Center longitude for radius query', required: false, schema: new OA\Schema(type: 'number', format: 'float')),
            new OA\Parameter(name: 'radius', in: 'query', description: 'Radius in meters', required: false, schema: new OA\Schema(type: 'number', format: 'float')),

            new OA\Parameter(name: 'bbEastLongitude', in: 'query', description: 'Bounding box east longitude', required: false, schema: new OA\Schema(type: 'number', format: 'float')),
            new OA\Parameter(name: 'bbWestLongitude', in: 'query', description: 'Bounding box west longitude', required: false, schema: new OA\Schema(type: 'number', format: 'float')),
            new OA\Parameter(name: 'bbNorthLatitude', in: 'query', description: 'Bounding box north latitude', required: false, schema: new OA\Schema(type: 'number', format: 'float')),
            new OA\Parameter(name: 'bbSouthLatitude', in: 'query', description: 'Bounding box south latitude', required: false, schema: new OA\Schema(type: 'number', format: 'float')),

            new OA\Parameter(name: 'orderBy', in: 'query', description: 'Property to sort by', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'orderDirection', in: 'query', description: 'asc or desc', required: false, schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'])),
            new OA\Parameter(name: 'distanceOrderDirection', in: 'query', description: 'Distance sort (with radius query)', required: false, schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'])),
            new OA\Parameter(name: 'startValue', in: 'query', description: 'Start value for ordered list', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'extended', in: 'query', description: 'Set true to retrieve a more detailed list', required: false, schema: new OA\Schema(type: 'boolean')),
            new OA\Parameter(name: 'size', in: 'query', description: 'Length of resulting list (default 10)', required: false, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Returned when successful'),
        ]
    )]
    public function listAction(Request $request, DataQueryManagerInterface $dataQueryManager): JsonResponse
    {
        $queryParameterList = RequestToListConverter::convert($request);
        $rideList = $dataQueryManager->query($queryParameterList, Ride::class);

        $groups = ['ride-list'];

        if ($request->query->has('extended') && $request->query->getBoolean('extended') === true) {
            $groups[] = 'extended-ride-list';
        }

        $context = new SerializationContext();
        $context->setGroups($groups);

        return $this->createStandardResponse($rideList, $context);
    }

    #[Route(path: '/{citySlug}/{rideIdentifier}', name: 'caldera_criticalmass_rest_ride_create', methods: ['PUT'])]
    #[OA\Put(
        path: '/{citySlug}/{rideIdentifier}',
        summary: 'Creates a new ride',
        parameters: [
            new OA\Parameter(
                name: 'citySlug',
                in: 'path',
                description: 'Slug of the city to assign the new created ride to',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'rideIdentifier',
                in: 'path',
                description: 'Identifier of the ride to be created (date or slug)',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        requestBody: new OA\RequestBody(
            description: 'JSON representation of ride',
            required: true,
            content: new OA\JsonContent(type: 'object') // optional: hier konkretes Schema ergänzen
        ),
        responses: [
            new OA\Response(response: 200, description: 'Returned when successful'),
            new OA\Response(response: 400, description: 'Validation failed'),
        ]
    )]
    public function createRideAction(Request $request, City $city, ValidatorInterface $validator): JsonResponse
    {
        /** @var Ride $ride */
        $ride = $this->deserializeRequest($request, Ride::class);
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

        if (count($errorList) > 0) {
            return $this->createErrors(JsonResponse::HTTP_BAD_REQUEST, $errorList);
        }

        $manager = $this->managerRegistry->getManager();
        $manager->persist($ride);
        $manager->flush();

        $context = new SerializationContext();
        $context->setGroups('ride-list');

        return $this->createStandardResponse($ride, $context);
    }

    #[Route(path: '/{citySlug}/{rideIdentifier}', name: 'caldera_criticalmass_rest_ride_update', methods: ['POST'])]
    #[OA\Post(
        path: '/{citySlug}/{rideIdentifier}',
        summary: 'Updates a ride',
        parameters: [
            new OA\Parameter(
                name: 'citySlug',
                in: 'path',
                description: 'Slug of the city to assign the updated ride to',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'rideIdentifier',
                in: 'path',
                description: 'Identifier of the ride to be updated',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        requestBody: new OA\RequestBody(
            description: 'JSON representation of ride',
            required: true,
            content: new OA\JsonContent(type: 'object') // optional: konkretes Schema ergänzen
        ),
        responses: [
            new OA\Response(response: 200, description: 'Returned when successful'),
            new OA\Response(response: 400, description: 'Validation failed'),
            new OA\Response(response: 404, description: 'Ride not found'),
        ]
    )]
    public function updateRideAction(Request $request, Ride $ride, ValidatorInterface $validator, EntityMergerInterface $entityMerger): JsonResponse
    {
        /** @var Ride $updatedRide */
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

        if (count($errorList) > 0) {
            return $this->createErrors(JsonResponse::HTTP_BAD_REQUEST, $errorList);
        }

        $manager = $this->managerRegistry->getManager();
        $manager->flush();

        $context = new SerializationContext();
        $context->setGroups('ride-list');

        return $this->createStandardResponse($ride, $context);
    }
}
