<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Ride;
use App\Entity\Subride;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function listSubrideAction(ManagerRegistry $registry, SerializerInterface $serializer, Ride $ride): JsonResponse
    {
        $subrideList = $registry->getRepository(Subride::class)->findByRide($ride);

        return new JsonResponse($serializer->serialize($subrideList, 'json'), JsonResponse::HTTP_OK, [], true);
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
    public function showSubrideAction(Subride $subride, SerializerInterface $serializer, UserInterface $user = null): JsonResponse
    {
        return new JsonResponse($serializer->serialize($subride, 'json'), JsonResponse::HTTP_OK, [], true);
    }
}
