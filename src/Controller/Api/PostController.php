<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Post;
use MalteHuebner\DataQueryBundle\DataQueryManager\DataQueryManagerInterface;
use MalteHuebner\DataQueryBundle\RequestParameterList\RequestToListConverter;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class PostController extends BaseController
{
    /**
     * Get a list of posts.
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
     * <li><code>citySlug</code>: Limit the resulting list to a city like <code>hamburg</code>, <code>new-york</code> or <code>muenchen</code>.</li>
     * <li><code>rideIdentifier</code>: Reduce the result list for posts related to this specified ride. Must be combined with <code>citySlug</code>.</li>
     * </ul>
     *
     * <strong>Date-related query parameters</strong>
     *
     * <ul>
     * <li><code>year</code>: Retrieve only posts created in the provided <code>year</code>.</li>
     * <li><code>month</code>: Retrieve only posts of the provided <code>year</code> and <code>month</code>. This will only work in combination with the previous <code>year</code> parameter.</li>
     * <li><code>day</code>: Limit the result list to a <code>day</code>. This parameter must be used with <code>year</code> and <code>month</code>.</li>
     * </ul>
     *
     * <strong>Geo query parameters</strong>
     *
     * <ul>
     * <li>Radius query: Specify <code>centerLatitude</code>, <code>centerLongitude</code> and a <code>radius</code> to retrieve all results within this circle.</li>
     * <li>Bounding Box query: Fetch all posts in the box described by <code>bbNorthLatitude</code>, <code>bbEastLongitude</code> and <code>bbSouthLatitude</code>, <code>bbWestLongitude</code>.
     * </ul>
     *
     * <strong>Order parameters</strong>
     *
     * Sort the resulting list with the parameter <code>orderBy</code> and choose from one of the following properties:
     *
     * <ul>
     * <li><code>id</code></li>
     * <li><code>dateTime</code></li>
     * <li><code>latitude</code></li>
     * <li><code>longitude</code></li>
     * </ul>
     *
     * Specify the order direction with <code>orderDirection=asc</code> or <code>orderDirection=desc</code>.
     *
     * Apply <code>startValue</code> to deliver a value to start your ordered list with.
     */
    #[Route(path: '/api/post', name: 'caldera_criticalmass_rest_post_list', methods: ['GET'], priority: 200)]
    #[OA\Tag(name: 'Post')]
    #[OA\Parameter(name: 'citySlug', in: 'query', description: 'Provide a city slug', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'rideIdentifier', in: 'query', description: 'Provide a ride identifier', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'year', in: 'query', description: 'Limit the result set to this year.', schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'month', in: 'query', description: 'Limit the result set to this month. Must be combined with year.', schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'day', in: 'query', description: 'Limit the result set to this day.', schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'centerLatitude', in: 'query', description: 'Latitude of a coordinate to search posts around in a given radius.', schema: new OA\Schema(type: 'number'))]
    #[OA\Parameter(name: 'centerLongitude', in: 'query', description: 'Longitude of a coordinate to search posts around in a given radius.', schema: new OA\Schema(type: 'number'))]
    #[OA\Parameter(name: 'radius', in: 'query', description: 'Radius to look around for posts.', schema: new OA\Schema(type: 'number'))]
    #[OA\Parameter(name: 'bbEastLongitude', in: 'query', description: 'East longitude of a bounding box to look for posts.', schema: new OA\Schema(type: 'number'))]
    #[OA\Parameter(name: 'bbWestLongitude', in: 'query', description: 'West longitude of a bounding box to look for posts.', schema: new OA\Schema(type: 'number'))]
    #[OA\Parameter(name: 'bbNorthLatitude', in: 'query', description: 'North latitude of a bounding box to look for posts.', schema: new OA\Schema(type: 'number'))]
    #[OA\Parameter(name: 'bbSouthLatitude', in: 'query', description: 'South latitude of a bounding box to look for posts.', schema: new OA\Schema(type: 'number'))]
    #[OA\Parameter(name: 'orderBy', in: 'query', description: 'Choose a property to sort the list by.', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'orderDirection', in: 'query', description: 'Sort ascending or descending.', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'startValue', in: 'query', description: 'Start ordered list with provided value.', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'size', in: 'query', description: 'Length of resulting list. Defaults to 10.', schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function listAction(Request $request, DataQueryManagerInterface $dataQueryManager): JsonResponse
    {
        $queryParameterList = RequestToListConverter::convert($request);

        $postList = $dataQueryManager->query($queryParameterList, Post::class);

        return $this->createStandardResponse($postList, ['groups' => ['post-list']]);
    }
}
