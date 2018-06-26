<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\City;
use AppBundle\Traits\RepositoryTrait;
use AppBundle\Traits\UtilTrait;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class CityController extends BaseController
{
    use RepositoryTrait;
    use UtilTrait;

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Returns a list of critical mass cities"
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
     * @ApiDoc(
     *  resource=true,
     *  description="Shows a critical mass city"
     * )
     * @ParamConverter("city", class="AppBundle:City")
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
