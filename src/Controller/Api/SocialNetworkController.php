<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\City;
use App\Entity\Location;
use App\Entity\SocialNetworkProfile;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

class SocialNetworkController extends BaseController
{
    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Retrieve a list of social network profiles assigned to a city",
     *  section="Social Network",
     *  requirements={
     *    {"name"="citySlug", "dataType"="string", "required"=true, "description"="Retrieve a list of social network profiles assigned to a city"}
     *  }
     * )
     * @ParamConverter("city", class="App:City")
     */
    public function listProfilesAction(ManagerRegistry $registry, City $city): Response
    {
        $profileList = $registry->getRepository(SocialNetworkProfile::class)->findAll();

        $view = View::create();
        $view
            ->setData($profileList)
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
