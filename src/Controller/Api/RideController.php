<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Criticalmass\DataQuery\Factory\ParameterFactory\ParameterFactoryInterface;
use App\Criticalmass\DataQuery\Factory\QueryFactory\QueryFactoryInterface;
use App\Criticalmass\DataQuery\FinderFactory\FinderFactoryInterface;
use App\Entity\City;
use App\Entity\Ride;
use App\Traits\RepositoryTrait;
use App\Traits\UtilTrait;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RideController extends BaseController
{
    use RepositoryTrait;
    use UtilTrait;

    /**
     * Retrieve information about a ride identified by <code>rideIdentifier</code> of a city identified by <code>citySlug</code>.
     *
     * As the parameter <code>citySlug</code> is just a string like <code>hamburg-harburg</code> or <code>muenchen</code> the parameter <code>rideIdentifier</code> is either the date of the ride like <code>2011-06-24</code> or a special identifier like <code>kidical-mass-hamburg-september-2019</code>.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns ride details",
     *  section="Ride"
     * )
     * @ParamConverter("ride", class="App:Ride")
     */
    public function showAction(Ride $ride): Response
    {
        $view = View::create();
        $view
            ->setData($ride)
            ->setFormat('json')
            ->setStatusCode(200);

        return $this->handleView($view);
    }

    /**
     * Retrieve information about the current ride of a city identified by <code>citySlug</code>.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns details of the next ride in the city",
     *  section="Ride"
     * )
     * @ParamConverter("city", class="App:City")
     */
    public function showCurrentAction(Request $request, City $city): Response
    {
        $ride = $this->getRideRepository()->findCurrentRideForCity($city, (bool) $request->get('cycleMandatory', false), (bool) $request->get('slugsAllowd', true));

        if (!$ride) {
            return new JsonResponse([], 200, []); // @todo this should return 404, but i have no clue how to handle multiple jquery requests then
        }

        $view = View::create();
        $view
            ->setData($ride)
            ->setFormat('json')
            ->setStatusCode(200);

        return $this->handleView($view);
    }

    /**
     * Get a list of critical mass rides.
     *
     * This list may be limited to city or region by providing a city or region slug. You may also limit the list to a specific month or a specific day.
     *
     * If you do not provide <code>year</code> and <code>month</code>, results will be limited to the current month.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Lists rides",
     *  parameters={
     *     {"name"="region", "dataType"="string", "required"=false, "description"="Provide a region slug"},
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
     *     {"name"="bbSouthLatitude", "dataType"="float", "required"=false, "description"="South latitude of a bounding box to look for rides."}
     *  },
     *  section="Ride"
     * )
     */
    public function listAction(Request $request, QueryFactoryInterface $queryFactory, ParameterFactoryInterface $parameterFactory, FinderFactoryInterface $finderFactory): Response
    {
        $queryList = $queryFactory->setEntityFqcn(Ride::class)->createFromRequest($request);
        $parameterList = $parameterFactory->setEntityFqcn(Ride::class)->createFromRequest($request);

        $finder = $finderFactory->createFinderForFqcn(Ride::class);
        $rideList = $finder->executeQuery($queryList, $parameterList);

        $context = new Context();
        $context->addGroup('ride-list');

        $view = View::create();
        $view
            ->setData($rideList)
            ->setFormat('json')
            ->setStatusCode(200)
            ->setContext($context);

        return $this->handleView($view);
    }
}
