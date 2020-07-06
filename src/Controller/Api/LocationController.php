<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\City;
use App\Entity\Location;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LocationController extends BaseController
{
    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Retrieve a list of locations of a city",
     *  section="Location",
     *  requirements={
     *    {"name"="citySlug", "dataType"="string", "required"=true, "description"="Provide the slug of a city."}
     *  }
     * )
     * @ParamConverter("city", class="App:City")
     * @Route("/{citySlug}/location", name="caldera_criticalmass_rest_location_list", methods={"GET"}, options={"expose"=true})
     */
    public function listLocationAction(ManagerRegistry $registry, City $city): Response
    {
        $locationList = $registry->getRepository(Location::class)->findLocationsByCity($city);

        $view = View::create();
        $view
            ->setData($locationList)
            ->setFormat('json')
            ->setStatusCode(200);

        return $this->handleView($view);
    }

    /**
     * Show details of a specified location.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Show details of a location",
     *  section="Location",
     *  requirements={
     *    {"name"="citySlug", "dataType"="string", "required"=true, "description"="Provide the slug of a city."},
     *    {"name"="locationSlug", "dataType"="string", "required"=true, "description"="Slug of the location."},
     *  }
     * )
     * @ParamConverter("location", class="App:Location")
     * @Route("/{citySlug}/location/{locationSlug}", name="caldera_criticalmass_rest_location_show", methods={"GET"}, options={"expose"=true})
     */
    public function showLocationAction(Location $location): Response
    {
        $context = new Context();

        $view = View::create();
        $view
            ->setData($location)
            ->setFormat('json')
            ->setStatusCode(200)
            ->setContext($context);

        return $this->handleView($view);
    }
}
