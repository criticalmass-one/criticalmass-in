<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Criticalmass\Timeline\TimelineInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class TimelineController extends BaseController
{
    private const int DEFAULT_LIMIT = 10;
    private const int DEFAULT_OFFSET = 0;

    #[Route(
        path: '/api/timeline',
        name: 'caldera_criticalmass_rest_timeline',
        methods: ['GET'],
        priority: 200
    )]
    public function timelineAction(Request $request, TimelineInterface $cachedTimeline): JsonResponse
    {
        $limit = $request->query->getInt('limit', self::DEFAULT_LIMIT);
        $offset = $request->query->getInt('offset', self::DEFAULT_OFFSET);

        $endDateTime = new \DateTime();
        $startDateTime = (clone $endDateTime)->sub(new \DateInterval('P3M'));

        $timelineContentList = $cachedTimeline
            ->setDateRange($startDateTime, $endDateTime)
            ->execute()
            ->getTimelineContentList();

        $total = count($timelineContentList);
        $items = array_slice($timelineContentList, $offset, $limit);
        $hasMore = ($offset + $limit) < $total;

        return new JsonResponse([
            'items' => $items,
            'hasMore' => $hasMore,
        ]);
    }
}
