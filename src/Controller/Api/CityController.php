<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Criticalmass\DataQuery\DataQueryManager\DataQueryManagerInterface;
use App\Entity\City;
use App\Traits\RepositoryTrait;
use App\Traits\UtilTrait;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CityController extends BaseController
{
    use RepositoryTrait;
    use UtilTrait;

    /**
     * This endpoint will return a list of all known critical mass cities known to our database.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Returns a list of critical mass cities",
     *  section="City",
     *  parameters={
     *     {"name"="region", "dataType"="string", "required"=false, "description"="Provide a region slug"},
     *     {"name"="centerLatitude", "dataType"="float", "required"=false, "description"="Latitude of a coordinate to search rides around in a given radius."},
     *     {"name"="centerLongitude", "dataType"="float", "required"=false, "description"="Longitude of a coordinate to search rides around in a given radius."},
     *     {"name"="radius", "dataType"="float", "required"=false, "description"="Radius to look around for rides."},
     *     {"name"="bbEastLongitude", "dataType"="float", "required"=false, "description"="East longitude of a bounding box to look for rides."},
     *     {"name"="bbWestLongitude", "dataType"="float", "required"=false, "description"="West longitude of a bounding box to look for rides."},
     *     {"name"="bbNorthLatitude", "dataType"="float", "required"=false, "description"="North latitude of a bounding box to look for rides."},
     *     {"name"="bbSouthLatitude", "dataType"="float", "required"=false, "description"="South latitude of a bounding box to look for rides."}
     *   }
     * )
     */
    public function listAction(Request $request, DataQueryManagerInterface $dataQueryManager): Response
    {
        $cityList = $dataQueryManager->queryForRequest($request, City::class);

        $view = View::create();
        $view
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
     *  section="City"
     * )
     * @ParamConverter("city", class="App:City")
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
