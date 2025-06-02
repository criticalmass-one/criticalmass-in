<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Ride;
use App\Entity\Subride;
use Nelmio\ApiDocBundle\Annotation\Operation;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class SubrideController extends BaseController
{
    /**
     * @Operation(
     *     tags={"Subride"},
     *     summary="Retrieve a list of subrides of a ride",
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     * @ParamConverter("ride", class="App:Ride")
     */
    #[Route(path: '/{citySlug}/{rideIdentifier}/subride', name: 'caldera_criticalmass_rest_subride_list', options: ['expose' => true], methods: ['GET'])]
    public function listSubrideAction(Ride $ride): JsonResponse
    {
        $subrideList = $this->managerRegistry->getRepository(Subride::class)->findByRide($ride);

        return $this->createStandardResponse($subrideList);
    }

    /**
     * Show details of a specified subride.
     *
     * @Operation(
     *     tags={"Subride"},
     *     summary="Show details of a subride",
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     * @ParamConverter("subride", class="App:Subride")
     */
    #[Route(path: '/{citySlug}/{rideIdentifier}/{subrideId}', name: 'caldera_criticalmass_rest_subride_show', requirements: ['subrideId' => '\d+'], methods: ['GET'])]
    public function showSubrideAction(Subride $subride): JsonResponse
    {
        return $this->createStandardResponse($subride);
    }
}
