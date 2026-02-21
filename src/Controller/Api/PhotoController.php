<?php declare(strict_types=1);

namespace App\Controller\Api;

use MalteHuebner\DataQueryBundle\DataQueryManager\DataQueryManagerInterface;
use MalteHuebner\DataQueryBundle\RequestParameterList\RequestToListConverter;
use App\Entity\Photo;
use App\Entity\Ride;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class PhotoController extends BaseController
{
    /**
     * Get a list of photos which were uploaded to a specified ride.
     */
    #[Route(path: '/api/{citySlug}/{rideIdentifier}/listPhotos', name: 'caldera_criticalmass_rest_photo_ridelist', methods: ['GET'], priority: 190)]
    #[OA\Tag(name: 'Photo')]
    #[OA\Parameter(name: 'citySlug', in: 'path', description: 'Provide a city slug', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'rideIdentifier', in: 'path', description: 'Provide a ride identifier', required: true, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function listRidePhotosAction(Ride $ride): JsonResponse
    {
        $photoList = $this->managerRegistry->getRepository(Photo::class)->findPhotosByRide($ride);

        return $this->createStandardResponse($photoList);
    }

    /**
     * Get a list of photos.
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
     * <li><code>regionSlug</code>: Provide a slug like <code>schleswig-holstein</code> to retrieve only photos from rides in this region.</li>
     * <li><code>citySlug</code>: Limit the resulting list to a city like <code>hamburg</code>, <code>new-york</code> or <code>muenchen</code>.</li>
     * <li><code>rideIdentifier</code>: Reduce the result list for photos uploaded to this specified ride. Must be combined with <code>citySlug</code>.</li>
     * </ul>
     *
     * <strong>Date-related query parameters</strong>
     *
     * <ul>
     * <li><code>year</code>: Retrieve only photos taken in the provided <code>year</code>.</li>
     * <li><code>month</code>: Retrieve only photos of the provided <code>year</code> and <code>month</code>. This will only work in combination with the previous <code>year</code> parameter.</li>
     * <li><code>day</code>: Limit the result list to a <code>day</code>. This parameter must be used with <code>year</code> and <code>month</code>.</li>
     * </ul>
     *
     * <strong>Geo query parameters</strong>
     *
     * <ul>
     * <li>Radius query: Specify <code>centerLatitude</code>, <code>centerLongitude</code> and a <code>radius</code> to retrieve all results within this circle.</li>
     * <li>Bounding Box query: Fetch all photos in the box described by <code>bbNorthLatitude</code>, <code>bbEastLongitude</code> and <code>bbSouthLatitude</code>, <code>bbWestLongitude</code>.
     * </ul>
     *
     * <strong>Order parameters</strong>
     *
     * Sort the resulting list with the parameter <code>orderBy</code> and choose from one of the following properties:
     *
     * <ul>
     * <li><code>id</code></li>
     * <li><code>latitude</code></li>
     * <li><code>longitude</code></li>
     * <li><code>description</code></li>
     * <li><code>creationDateTime</code></li>
     * <li><code>imageSize</code></li>
     * <li><code>updatedAt</code></li>
     * <li><code>location</code></li>
     * <li><code>exifExposure</code></li>
     * <li><code>exifAperture</code></li>
     * <li><code>exifIso</code></li>
     * <li><code>exifFocalLength</code></li>
     * <li><code>exifCamera</code></li>
     * <li><code>exifCreationDate</code></li>
     * </ul>
     *
     * Specify the order direction with <code>orderDirection=asc</code> or <code>orderDirection=desc</code>.
     *
     * You may use the <code>distanceOrderDirection</code> parameter in combination with the radius query to sort the result list by the photos geo position to the center coord.
     *
     * Apply <code>startValue</code> to deliver a value to start your ordered list with.
     */
    #[Route(path: '/api/photo', name: 'caldera_criticalmass_rest_photo_list', methods: ['GET'], priority: 200)]
    #[OA\Tag(name: 'Photo')]
    #[OA\Parameter(name: 'regionSlug', in: 'query', description: 'Provide a region slug', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'citySlug', in: 'query', description: 'Provide a city slug', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'rideIdentifier', in: 'query', description: 'Provide a ride identifier', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'year', in: 'query', description: 'Limit the result set to this year.', schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'month', in: 'query', description: 'Limit the result set to this month. Must be combined with year.', schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'day', in: 'query', description: 'Limit the result set to this day.', schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'centerLatitude', in: 'query', description: 'Latitude of a coordinate to search photos around in a given radius.', schema: new OA\Schema(type: 'number'))]
    #[OA\Parameter(name: 'centerLongitude', in: 'query', description: 'Longitude of a coordinate to search photos around in a given radius.', schema: new OA\Schema(type: 'number'))]
    #[OA\Parameter(name: 'radius', in: 'query', description: 'Radius to look around for photos.', schema: new OA\Schema(type: 'number'))]
    #[OA\Parameter(name: 'bbEastLongitude', in: 'query', description: 'East longitude of a bounding box to look for photos.', schema: new OA\Schema(type: 'number'))]
    #[OA\Parameter(name: 'bbWestLongitude', in: 'query', description: 'West longitude of a bounding box to look for photos.', schema: new OA\Schema(type: 'number'))]
    #[OA\Parameter(name: 'bbNorthLatitude', in: 'query', description: 'North latitude of a bounding box to look for photos.', schema: new OA\Schema(type: 'number'))]
    #[OA\Parameter(name: 'bbSouthLatitude', in: 'query', description: 'South latitude of a bounding box to look for photos.', schema: new OA\Schema(type: 'number'))]
    #[OA\Parameter(name: 'orderBy', in: 'query', description: 'Choose a property to sort the list by.', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'orderDirection', in: 'query', description: 'Sort ascending or descending.', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'distanceOrderDirection', in: 'query', description: 'Enable distance sorting in combination with radius query.', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'startValue', in: 'query', description: 'Start ordered list with provided value.', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'size', in: 'query', description: 'Length of resulting list. Defaults to 10.', schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function listAction(Request $request, DataQueryManagerInterface $dataQueryManager): JsonResponse
    {
        $queryParameterList = RequestToListConverter::convert($request);

        $photoList = $dataQueryManager->query($queryParameterList, Photo::class);

        return $this->createStandardResponse($photoList);
    }

    /**
     * Update properties of a photo.
     */
    #[Route(path: '/api/photo/{id}', name: 'caldera_criticalmass_rest_photo_post', methods: ['POST'], priority: 200)]
    #[OA\Tag(name: 'Photo')]
    #[OA\Parameter(name: 'id', in: 'path', description: 'Id of the photo to update', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(description: 'JSON representation of the photo data', required: true, content: new OA\JsonContent(type: 'object'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function updatePhotoAction(Request $request): JsonResponse
    {
        $photo = $this->deserializeRequest($request, Photo::class);

        return $this->createStandardResponse($photo);
    }
}
