<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\City;
use App\Entity\CityCycle;
use App\Entity\Region;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Attribute\Route;

class CycleController extends BaseController
{
    /**
     * Returns a list of city cycles.
     */
    #[Route(path: '/api/cycles', name: 'caldera_criticalmass_rest_cycles_list', methods: ['GET'], priority: 200)]
    #[OA\Tag(name: 'Cycles')]
    #[OA\Parameter(name: 'citySlug', in: 'query', description: 'Provide a city slug', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'regionSlug', in: 'query', description: 'Provide a region slug', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'validFrom', in: 'query', description: 'Only retrieve cycles valid after the provided date', schema: new OA\Schema(type: 'string', format: 'date'))]
    #[OA\Parameter(name: 'validUntil', in: 'query', description: 'Only retrieve cycles valid before the provided date', schema: new OA\Schema(type: 'string', format: 'date'))]
    #[OA\Parameter(name: 'validNow', in: 'query', description: 'Only retrieve cycles valid for the current month', schema: new OA\Schema(type: 'boolean'))]
    #[OA\Parameter(name: 'dayOfWeek', in: 'query', description: 'Limit the results to this day of week', schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'weekOfMonth', in: 'query', description: 'Limit the results to this week of month', schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Returned when successful')]
    public function listAction(
        Request $request,
        ?City $city = null,
        ?Region $region = null
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
