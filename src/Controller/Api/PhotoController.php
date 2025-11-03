<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Photo;
use App\Entity\Ride;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\SerializerInterface;
use MalteHuebner\DataQueryBundle\DataQueryManager\DataQueryManagerInterface;
use MalteHuebner\DataQueryBundle\RequestParameterList\RequestToListConverter;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Photo')]
class PhotoController extends BaseController
{
    #[Route(path: '/{citySlug}/{rideIdentifier}/listPhotos', name: 'caldera_criticalmass_rest_photo_ridelist', methods: ['GET'])]
    #[OA\Get(
        path: '/api/{citySlug}/{rideIdentifier}/listPhotos',
        summary: 'Retrieve a list of photos of a ride',
        parameters: [
            new OA\Parameter(
                name: 'citySlug',
                description: 'Provide a city slug',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'rideIdentifier',
                description: 'Provide a ride identifier',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Returned when successful'),
        ]
    )]
    public function listRidePhotosAction(ManagerRegistry $registry, Ride $ride): JsonResponse
    {
        $photoList = $registry->getRepository(Photo::class)->findPhotosByRide($ride);

        return $this->createStandardResponse($photoList);
    }

    #[Route(path: '/photo/{id}', name: 'caldera_criticalmass_rest_photo_show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/photo/{id}',
        summary: 'Retrieve a photo by its id',
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Photo id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Returned when successful'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function showPhotoAction(Photo $photo): JsonResponse
    {
        return $this->createStandardResponse($photo);
    }

    #[Route(path: '/photo', name: 'caldera_criticalmass_rest_photo_list', methods: ['GET'])]
    #[OA\Get(
        path: '/api/photo',
        summary: 'Lists photos',
        parameters: [
            // Regional
            new OA\Parameter(name: 'regionSlug', in: 'query', description: 'Provide a region slug', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'citySlug', in: 'query', description: 'Provide a city slug', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'rideIdentifier', in: 'query', description: 'Provide a ride identifier (requires citySlug)', required: false, schema: new OA\Schema(type: 'string')),
            // Date
            new OA\Parameter(name: 'year', in: 'query', description: 'Year filter', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'month', in: 'query', description: 'Month filter (requires year)', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'day', in: 'query', description: 'Day filter (requires year and month)', required: false, schema: new OA\Schema(type: 'integer')),
            // Geo (radius)
            new OA\Parameter(name: 'centerLatitude', in: 'query', description: 'Center latitude for radius query', required: false, schema: new OA\Schema(type: 'number', format: 'float')),
            new OA\Parameter(name: 'centerLongitude', in: 'query', description: 'Center longitude for radius query', required: false, schema: new OA\Schema(type: 'number', format: 'float')),
            new OA\Parameter(name: 'radius', in: 'query', description: 'Radius in meters', required: false, schema: new OA\Schema(type: 'number', format: 'float')),
            // Geo (bbox)
            new OA\Parameter(name: 'bbEastLongitude', in: 'query', description: 'Bounding box east longitude', required: false, schema: new OA\Schema(type: 'number', format: 'float')),
            new OA\Parameter(name: 'bbWestLongitude', in: 'query', description: 'Bounding box west longitude', required: false, schema: new OA\Schema(type: 'number', format: 'float')),
            new OA\Parameter(name: 'bbNorthLatitude', in: 'query', description: 'Bounding box north latitude', required: false, schema: new OA\Schema(type: 'number', format: 'float')),
            new OA\Parameter(name: 'bbSouthLatitude', in: 'query', description: 'Bounding box south latitude', required: false, schema: new OA\Schema(type: 'number', format: 'float')),
            // Ordering / paging
            new OA\Parameter(name: 'orderBy', in: 'query', description: 'Property to sort by', required: false, schema: new OA\Schema(type: 'string', enum: [
                'id', 'latitude', 'longitude', 'description', 'views', 'creationDateTime', 'imageSize', 'updatedAt', 'location',
                'exifExposure', 'exifAperture', 'exifIso', 'exifFocalLength', 'exifCamera', 'exifCreationDate',
            ])),
            new OA\Parameter(name: 'orderDirection', in: 'query', description: 'asc or desc', required: false, schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'])),
            new OA\Parameter(name: 'distanceOrderDirection', in: 'query', description: 'Distance sort (with radius query)', required: false, schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'])),
            new OA\Parameter(name: 'startValue', in: 'query', description: 'Start value for ordered list', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'size', in: 'query', description: 'Length of resulting list (default 10)', required: false, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Returned when successful'),
        ]
    )]
    public function listAction(Request $request, DataQueryManagerInterface $dataQueryManager): JsonResponse
    {
        $queryParameterList = RequestToListConverter::convert($request);
        $photoList = $dataQueryManager->query($queryParameterList, Photo::class);

        return $this->createStandardResponse($photoList);
    }

    #[Route(path: '/photo/{id}', name: 'caldera_criticalmass_rest_photo_post', methods: ['POST'])]
    #[OA\Post(
        path: '/api/photo/{id}',
        summary: 'Update a photo by payload (JSON)',
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Photo id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 123),
                    new OA\Property(property: 'description', type: 'string', example: 'Sunset in Hamburg'),
                    new OA\Property(property: 'latitude', type: 'number', format: 'float', example: 53.55),
                    new OA\Property(property: 'longitude', type: 'number', format: 'float', example: 9.99),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Returned when successful'),
            new OA\Response(response: 400, description: 'Invalid payload'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function updatePhotoAction(Request $request, SerializerInterface $serializer): JsonResponse
    {
        $json = $request->getContent();
        /** @var Photo $photo */
        $photo = $serializer->deserialize($json, Photo::class, 'json');

        // Achtung: hier nur Echo zurück; eigentliche Persistierung erfolgt andernorts.
        return $this->createStandardResponse($photo);
    }
}
