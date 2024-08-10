<?php declare(strict_types=1);

namespace App\Controller\Api;

use JMS\Serializer\SerializationContext;
use MalteHuebner\DataQueryBundle\DataQueryManager\DataQueryManagerInterface;
use MalteHuebner\DataQueryBundle\RequestParameterList\RequestToListConverter;
use App\Entity\City;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: "City")]
class CityController extends BaseController
{
    /**
     * Get a list of critical mass cities.
     *
     * You may specify your query with the following parameters.
     *
     * <strong>Name</strong>
     *
     * Find a city by it's name with the <code>name</code> parameter.
     *
     * <strong>Regional query parameters</strong>
     *
     * Provide a <code>regionSlug</code> like <code>schleswig-holstein</code> to retrieve only cities of this region.
     *
     * <strong>List length</strong>
     *
     * The length of your results defaults to 10. Use <code>size</code> to request more or less results.
     *
     * <strong>Geo query parameters</strong>
     *
     * <ul>
     * <li>Radius query: Specify <code>centerLatitude</code>, <code>centerLongitude</code> and a <code>radius</code> to retrieve all results within this circle.</li>
     * <li>Bounding Box query: Fetch all cities in the box described by <code>bbNorthLatitude</code>, <code>bbEastLongitude</code> and <code>bbSouthLatitude</code>, <code>bbWestLongitude</code>.
     * </ul>
     *
     * <strong>Order parameters</strong>
     *
     * Sort the resulting list with the parameter <code>orderBy</code> and choose from one of the following properties:
     *
     * <ul>
     * <li><code>id</code></li>
     * <li><code>region</code></li>
     * <li><code>name</code></li>
     * <li><code>title</code></li>
     * <li><code>cityPopulation</code></li>
     * <li><code>latitude</code></li>
     * <li><code>longitude</code></li>
     * <li><code>updatedAt</code></li>
     * <li><code>createdAt</code></li>
     * </ul>
     *
     * Specify the order direction with <code>orderDirection=asc</code> or <code>orderDirection=desc</code>.
     *
     * You may use the <code>distanceOrderDirection</code> parameter in combination with the radius query to sort the result list by the city’s distance to the center coord.
     *
     * Apply <code>startValue</code> to deliver a value to start your ordered list with.
     */
    #[OA\Parameter(
        name: "name",
        description: "Name of the city.",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "string")
    )]
    #[OA\Parameter(
        name: "regionSlug",
        description: "Provide a region slug.",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "string")
    )]
    #[OA\Parameter(
        name: "centerLatitude",
        description: "Latitude of a coordinate to search cities around in a given radius.",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "number")
    )]
    #[OA\Parameter(
        name: "centerLongitude",
        description: "Longitude of a coordinate to search cities around in a given radius.",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "number")
    )]
    #[OA\Parameter(
        name: "radius",
        description: "Radius to look around for cities.",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "number")
    )]
    #[OA\Parameter(
        name: "bbEastLongitude",
        description: "East longitude of a bounding box to look for cities.",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "number")
    )]
    #[OA\Parameter(
        name: "bbWestLongitude",
        description: "West longitude of a bounding box to look for cities.",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "number")
    )]
    #[OA\Parameter(
        name: "bbNorthLatitude",
        description: "North latitude of a bounding box to look for cities.",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "number")
    )]
    #[OA\Parameter(
        name: "bbSouthLatitude",
        description: "South latitude of a bounding box to look for cities.",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "number")
    )]
    #[OA\Parameter(
        name: "orderBy",
        description: "Choose a property to sort the list by.",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "string")
    )]
    #[OA\Parameter(
        name: "orderDirection",
        description: "Sort ascending or descending.",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "string")
    )]
    #[OA\Parameter(
        name: "distanceOrderDirection",
        description: "Enable distance sorting in combination with radius query.",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "string")
    )]
    #[OA\Parameter(
        name: "startValue",
        description: "Start ordered list with provided value.",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "string")
    )]
    #[OA\Parameter(
        name: "size",
        description: "Length of resulting list. Defaults to 10.",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "integer")
    )]
    #[OA\Parameter(
        name: "extended",
        description: "Set true to retrieve a more detailed list.",
        in: "query",
        required: false,
        schema: new OA\Schema(type: "boolean")
    )]
    #[OA\Response(
        response: 200,
        description: "Returned when successful."
    )]
    #[Route(
        path: "/city",
        name: "caldera_criticalmass_rest_city_list",
        methods: ["GET"]
    )]
    public function listAction(Request $request, DataQueryManagerInterface $dataQueryManager): JsonResponse
    {
        $queryParameterList = RequestToListConverter::convert($request);
        $cityList = $dataQueryManager->query($queryParameterList, City::class);

        $groups = ['ride-list'];

        if ($request->query->has('extended') && true === $request->query->getBoolean('extended')) {
            $groups[] = 'extended-ride-list';
        }

        $context = new SerializationContext();
        $context->setGroups($groups);

        return $this->createStandardResponse($cityList, $context);
    }

    /**
     * Retrieve information for a city, which is identified by the parameter <code>citySlug</code>.
     *
     * @ParamConverter("city", class="App:City")
     */
    #[OA\Parameter(
        name: "citySlug",
        description: "Slug of the city.",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "string")
    )]
    #[OA\Response(
        response: 200,
        description: "Returned when successful."
    )]
    #[Route(path: '/{citySlug}', name: 'caldera_criticalmass_rest_city_show', methods: ['GET'], options: ['expose' => true])]
    public function showAction(City $city): JsonResponse
    {
        return $this->createStandardResponse($city);
    }
}
