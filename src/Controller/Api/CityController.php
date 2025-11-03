<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\City;
use JMS\Serializer\SerializationContext;
use MalteHuebner\DataQueryBundle\DataQueryManager\DataQueryManagerInterface;
use MalteHuebner\DataQueryBundle\RequestParameterList\RequestToListConverter;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'City')]
class CityController extends BaseController
{
    #[Route(
        path: '/api/city',
        name: 'caldera_criticalmass_rest_city_list',
        methods: ['GET']
    )]
    #[OA\Get(
        path: '/api/city',
        summary: 'Get a list of critical mass cities.',
        description: 'Filter per name/region, geo (radius/bbox), sortierung, paging, etc.',
        parameters: [
            new OA\Parameter(name: 'name', in: 'query', description: 'Name of the city.', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'regionSlug', in: 'query', description: 'Provide a region slug.', required: false, schema: new OA\Schema(type: 'string')),

            new OA\Parameter(name: 'centerLatitude', in: 'query', description: 'Latitude for radius query.', required: false, schema: new OA\Schema(type: 'number')),
            new OA\Parameter(name: 'centerLongitude', in: 'query', description: 'Longitude for radius query.', required: false, schema: new OA\Schema(type: 'number')),
            new OA\Parameter(name: 'radius', in: 'query', description: 'Radius to look around for cities.', required: false, schema: new OA\Schema(type: 'number')),

            new OA\Parameter(name: 'bbEastLongitude', in: 'query', description: 'BBox east longitude.', required: false, schema: new OA\Schema(type: 'number')),
            new OA\Parameter(name: 'bbWestLongitude', in: 'query', description: 'BBox west longitude.', required: false, schema: new OA\Schema(type: 'number')),
            new OA\Parameter(name: 'bbNorthLatitude', in: 'query', description: 'BBox north latitude.', required: false, schema: new OA\Schema(type: 'number')),
            new OA\Parameter(name: 'bbSouthLatitude', in: 'query', description: 'BBox south latitude.', required: false, schema: new OA\Schema(type: 'number')),

            new OA\Parameter(name: 'orderBy', in: 'query', description: 'Property to sort by.', required: false, schema: new OA\Schema(type: 'string', enum: [
                'id', 'region', 'name', 'title', 'cityPopulation', 'latitude', 'longitude', 'updatedAt', 'createdAt',
            ])),
            new OA\Parameter(name: 'orderDirection', in: 'query', description: 'asc or desc.', required: false, schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'])),
            new OA\Parameter(name: 'distanceOrderDirection', in: 'query', description: 'Distance sort (with radius query).', required: false, schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'])),
            new OA\Parameter(name: 'startValue', in: 'query', description: 'Start value for ordered list.', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'size', in: 'query', description: 'Length of resulting list (default 10).', required: false, schema: new OA\Schema(type: 'integer')),

            new OA\Parameter(name: 'extended', in: 'query', description: 'Set true to retrieve a more detailed list.', required: false, schema: new OA\Schema(type: 'boolean')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Returned when successful.'),
        ]
    )]
    public function listAction(Request $request, DataQueryManagerInterface $dataQueryManager): JsonResponse
    {
        $queryParameterList = RequestToListConverter::convert($request);
        $cityList = $dataQueryManager->query($queryParameterList, City::class);

        $groups = ['ride-list'];

        if ($request->query->has('extended') && $request->query->getBoolean('extended') === true) {
            $groups[] = 'extended-ride-list';
        }

        $context = new SerializationContext();
        $context->setGroups($groups);

        return $this->createStandardResponse($cityList, $context);
    }

    #[Route(
        path: '/api/city/{citySlug}',
        name: 'caldera_criticalmass_rest_city_show',
        methods: ['GET']
    )]
    #[OA\Get(
        path: '/api/city/{citySlug}',
        summary: 'Retrieve information for a city identified by its slug.',
        parameters: [
            new OA\Parameter(
                name: 'citySlug',
                description: 'Slug of the city.',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Returned when successful.'),
            new OA\Response(response: 404, description: 'Not found.'),
        ]
    )]
    public function showAction(City $city): JsonResponse
    {
        return $this->createStandardResponse($city);
    }
}
