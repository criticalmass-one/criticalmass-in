<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Ride;
use App\Entity\Subride;
use Doctrine\Persistence\ManagerRegistry;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class SubrideController extends BaseController
{
    /**
     * @Operation(
     *     tags={"Subride"},
     *     summary="Retrieve a list of subrides of a ride",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @ParamConverter("ride", class="App:Ride")
     * @Route("/{citySlug}/{rideIdentifier}/subride", name="caldera_criticalmass_rest_subride_list", methods={"GET"}, options={"expose"=true})
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
     * @Operation(
     *     tags={"Subride"},
     *     summary="Show details of a subride",
     *     @SWG\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     *
     * @ParamConverter("subride", class="App:Subride")
     * @Route("/{citySlug}/{rideIdentifier}/{subrideId}", name="caldera_criticalmass_rest_subride_show", methods={"GET"})
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
