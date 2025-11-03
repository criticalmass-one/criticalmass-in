<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\City;
use App\Entity\CityCycle;
use App\Entity\Region;
use Doctrine\Persistence\ManagerRegistry;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[OA\Tag(name: 'Cycles')]
class CycleController extends BaseController
{
    #[Route(path: '/cycles', name: 'caldera_criticalmass_rest_cycles_list', methods: ['GET'])]
    #[OA\Get(
        path: '/cycles',
        summary: 'Returns a list of city cycles',
        parameters: [
            new OA\Parameter(
                name: 'citySlug',
                in: 'query',
                description: 'Provide a city slug',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'regionSlug',
                in: 'query',
                description: 'Provide a region slug',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'validFrom',
                in: 'query',
                description: 'Only retrieve cycles valid on/after the provided date (YYYY-MM-DD)',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'date')
            ),
            new OA\Parameter(
                name: 'validUntil',
                in: 'query',
                description: 'Only retrieve cycles valid on/before the provided date (YYYY-MM-DD)',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'date')
            ),
            new OA\Parameter(
                name: 'validNow',
                in: 'query',
                description: 'Only retrieve cycles valid for the current month',
                required: false,
                schema: new OA\Schema(type: 'boolean')
            ),
            new OA\Parameter(
                name: 'dayOfWeek',
                in: 'query',
                description: 'Limit the results to this day of week (1=Mon … 7=Sun)',
                required: false,
                schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 7)
            ),
            new OA\Parameter(
                name: 'weekOfMonth',
                in: 'query',
                description: 'Limit the results to this week of month (1–5)',
                required: false,
                schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 5)
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Returned when successful'),
        ]
    )]
    public function listAction(Request $request, ManagerRegistry $registry): JsonResponse
    {
        $city = null;
        if ($citySlug = $request->query->get('citySlug')) {
            $city = $registry->getRepository(City::class)->findOneBy(['slug' => $citySlug]);
        }

        $region = null;
        if ($regionSlug = $request->query->get('regionSlug')) {
            $region = $registry->getRepository(Region::class)->findOneBy(['slug' => $regionSlug]);
        }

        $validNow    = $request->query->getBoolean('validNow');
        $dayOfWeek   = $request->query->has('dayOfWeek') ? $request->query->getInt('dayOfWeek') : null;
        $weekOfMonth = $request->query->has('weekOfMonth') ? $request->query->getInt('weekOfMonth') : null;

        $validFrom = null;
        if ($vf = $request->query->get('validFrom')) {
            $dt = \DateTimeImmutable::createFromFormat('Y-m-d', $vf) ?: new \DateTimeImmutable($vf);
            if ($dt !== false) {
                $validFrom = $dt instanceof \DateTimeImmutable ? \DateTime::createFromImmutable($dt) : $dt;
            }
        }

        $validUntil = null;
        if ($vu = $request->query->get('validUntil')) {
            $dt = \DateTimeImmutable::createFromFormat('Y-m-d', $vu) ?: new \DateTimeImmutable($vu);
            if ($dt !== false) {
                $validUntil = $dt instanceof \DateTimeImmutable ? \DateTime::createFromImmutable($dt) : $dt;
            }
        }

        $repo = $registry->getRepository(CityCycle::class);
        $cycleList = $repo->findForApi(
            $city,
            $region,
            $validFrom,
            $validUntil,
            $validNow,
            $dayOfWeek,
            $weekOfMonth
        );

        return $this->createStandardResponse($cycleList);
    }
}
