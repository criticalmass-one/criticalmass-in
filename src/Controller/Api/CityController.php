<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\City;
use App\Traits\RepositoryTrait;
use App\Traits\UtilTrait;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
     *  section="City"
     * )
     */
    public function listAction(): Response
    {
        $cityList = $this->getCityRepository()->findEnabledCities();

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
