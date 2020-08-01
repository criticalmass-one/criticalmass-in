<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Ride;
use App\Entity\Subride;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class SubrideController extends BaseController
{
    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Retrieve a list of subrides of a ride",
     *  section="Subride",
     *  requirements={
     *    {"name"="citySlug", "dataType"="string", "required"=true, "description"="Provide the slug of a city."},
     *    {"name"="rideIdentifier", "dataType"="string", "required"=true, "description"="Provide the ride identifier of a ride."},
     *  }
     * )
     * @ParamConverter("ride", class="App:Ride")
     */
    public function listSubrideAction(ManagerRegistry $registry, Ride $ride): Response
    {
        $subrideList = $registry->getRepository(Subride::class)->findByRide($ride);

        $view = View::create();
        $view
            ->setData($subrideList)
            ->setFormat('json')
            ->setStatusCode(Response::HTTP_OK);

        return $this->handleView($view);
    }

    /**
     * Show details of a specified subride.
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Show details of a subride",
     *  section="Subride",
     *  requirements={
     *    {"name"="citySlug", "dataType"="string", "required"=true, "description"="Provide the slug of a city."},
     *    {"name"="rideIdentifier", "dataType"="string", "required"=true, "description"="Provide the ride identifier of a ride."},
     *    {"name"="subrideId", "dataType"="integer", "required"=true, "description"="Unique id of the subride."},
     *  }
     * )
     * @ParamConverter("subride", class="App:Subride")
     */
    public function showSubrideAction(Subride $subride, UserInterface $user = null): Response
    {
        $context = new Context();

        $view = View::create();
        $view
            ->setData($subride)
            ->setFormat('json')
            ->setStatusCode(Response::HTTP_OK)
            ->setContext($context);

        return $this->handleView($view);
    }
}
