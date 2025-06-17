<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\City;
use App\Entity\CityCycle;
use App\Entity\Region;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\Operation;
use OpenApi\Annotations as OA;
use Symfony\Component\Routing\Annotation\Route;

class CycleController extends BaseController
{
    /**
     * @Operation(
     *     tags={"Cycles"},
     *     summary="Returns a list of city cycles",
     *     @OA\Parameter(
     *         name="citySlug",
     *         in="query",
     *         description="Provide a city slug",
     *         required=false,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Parameter(
     *         name="regionSlug",
     *         in="query",
     *         description="Provide a region slug",
     *         required=false,
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Parameter(
     *         name="validFrom",
     *         in="query",
     *         description="Only retrieve cycles valid after the provied date",
     *         required=false,
     *         @OA\Schema(type="date"),
     *     ),
     *     @OA\Parameter(
     *         name="validUntil",
     *         in="query",
     *         description="Only retrieve cycles valid before the provied date",
     *         required=false,
     *         @OA\Schema(type="date"),
     *     ),
     *     @OA\Parameter(
     *         name="validNow",
     *         in="query",
     *         description="Only retrieve cycles valid for the current month",
     *         required=false,
     *         @OA\Schema(type="bool"),
     *     ),
     *     @OA\Parameter(
     *         name="dayOfWeek",
     *         in="query",
     *         description="Limit the results to this day of week",
     *         required=false,
     *         @OA\Schema(type="int"),
     *     ),
     *     @OA\Parameter(
     *         name="weekOfMonth",
     *         in="query",
     *         description="Limit the results to this week of month",
     *         required=false,
     *         @OA\Schema(type="int"),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returned when successful"
     *     )
     * )
     */
    #[Route(path: '/cycles', name: 'caldera_criticalmass_rest_cycles_list', methods: ['GET'])]
    public function listAction(
        Request $request,
        City $city = null,
        Region $region = null
    ): JsonResponse {
        $validNow = $request->query->getBoolean('validNow');
        $dayOfWeek = $request->query->getInt('dayOfWeek');
        $weekOfMonth = $request->query->getInt('weekOfMonth');
        $validFromString = $request->query->getAlnum('validFrom');
        $validUntilString = $request->query->getAlnum('validUntil');

        if ($validFromString) {
            $validFrom = new \DateTime($validFromString);
        } else {
            $validFrom = null;
        }

        if ($validUntilString) {
            $validUntil = new \DateTime($validUntilString);
        } else {
            $validUntil = null;
        }

        $cycleList = $this->managerRegistry->getRepository(CityCycle::class)->findForApi($city, $region, $validFrom, $validUntil, $validNow, $dayOfWeek, $weekOfMonth);

        return $this->createStandardResponse($cycleList);
    }
}
