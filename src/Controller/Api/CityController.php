<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Criticalmass\DataQuery\DataQueryManager\DataQueryManagerInterface;
use App\Criticalmass\DataQuery\RequestParameterList\RequestToListConverter;
use App\Entity\City;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CityController extends BaseController
{
    /**
     * Get a list of critical mass cities.
     *
     * You may specify your query with the following parameters.
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
     * <li><code>city</code></li>
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
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns a list of critical mass cities",
     *  section="City",
     *  parameters={
     *     {"name"="regionSlug", "dataType"="string", "required"=false, "description"="Provide a region slug"},
     *     {"name"="centerLatitude", "dataType"="float", "required"=false, "description"="Latitude of a coordinate to search cities around in a given radius."},
     *     {"name"="centerLongitude", "dataType"="float", "required"=false, "description"="Longitude of a coordinate to search cities around in a given radius."},
     *     {"name"="radius", "dataType"="float", "required"=false, "description"="Radius to look around for cities."},
     *     {"name"="bbEastLongitude", "dataType"="float", "required"=false, "description"="East longitude of a bounding box to look for cities."},
     *     {"name"="bbWestLongitude", "dataType"="float", "required"=false, "description"="West longitude of a bounding box to look for cities."},
     *     {"name"="bbNorthLatitude", "dataType"="float", "required"=false, "description"="North latitude of a bounding box to look for cities."},
     *     {"name"="bbSouthLatitude", "dataType"="float", "required"=false, "description"="South latitude of a bounding box to look for cities."},
     *     {"name"="orderBy", "dataType"="string", "required"=false, "description"="Choose a property to sort the list by."},
     *     {"name"="orderDirection", "dataType"="string", "required"=false, "description"="Sort ascending or descending."},
     *     {"name"="distanceOrderDirection", "dataType"="string", "required"=false, "description"="Enable distance sorting in combination with radius query."},
     *     {"name"="startValue", "dataType"="string", "required"=false, "description"="Start ordered list with provided value."},
     *     {"name"="size", "dataType"="integer", "required"=false, "description"="Length of resulting list. Defaults to 10."},
     *     {"name"="extended", "dataType"="boolean", "required"=false, "description"="Set true to retrieve a more detailed list."}
     *  },
     * )
     * @Route("/{citySlug}/{rideIdentifier}", name="caldera_criticalmass_rest_city_list", methods={"GET"})
     */
    public function listAction(Request $request, DataQueryManagerInterface $dataQueryManager): Response
    {
        $queryParameterList = RequestToListConverter::convert($request);
        $cityList = $dataQueryManager->query($queryParameterList, City::class);

        $context = new Context();

        if ($request->query->has('extended') && true === $request->query->getBoolean('extended')) {
            $context->addGroup('extended-ride-list');
        }

        $context->addGroup('ride-list');

        $view = View::create();
        $view
            ->setContext($context)
            ->setData($cityList)
            ->setFormat('json')
            ->setStatusCode(200);

        return $this->handleView($view);
    }

    /**
     * Retrieve information for a city, which is identified by the parameter <code>citySlug</code>.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Shows a critical mass city",
     *  section="City",
     *  requirements={
     *    {"name"="citySlug", "dataType"="string", "required"=true, "description"="Provide the slug of a city."}
     *  }
     * )
     * @ParamConverter("city", class="App:City")
     * @Route("/{citySlug}", name="caldera_criticalmass_rest_city_show", methods={"GET"}, options={"expose"=true})
     */
    public function showAction(City $city): Response
    {
        $view = View::create();
        $view
            ->setData($city)
            ->setFormat('json')
            ->setStatusCode(200);

        return $this->handleView($view);
    }
}
