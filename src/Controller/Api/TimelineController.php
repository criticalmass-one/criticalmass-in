<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Criticalmass\Timeline\TimelineInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class TimelineController extends BaseController
{
    private const string LOWER_LIMIT = '2010-01-01';

    #[Route(
        path: '/api/timeline',
        name: 'caldera_criticalmass_rest_timeline',
        methods: ['GET'],
        priority: 200
    )]
    public function timelineAction(Request $request, TimelineInterface $cachedTimeline): JsonResponse
    {
        $year = $request->query->getInt('year');
        $month = $request->query->getInt('month');

        if ($year < 2010 || $month < 1 || $month > 12) {
            return new JsonResponse(['error' => 'Invalid date format'], JsonResponse::HTTP_NOT_FOUND);
        }

        $lowerLimitDateTime = new \DateTime(self::LOWER_LIMIT);

        $startDateTime = new \DateTime(sprintf('%04d-%02d-01', $year, $month));

        if ($startDateTime < $lowerLimitDateTime) {
            return new JsonResponse(['error' => 'Invalid date format'], JsonResponse::HTTP_NOT_FOUND);
        }

        $endDateTime = new \DateTime(sprintf('%04d-%02d-%s', $year, $month, $startDateTime->format('t')));

        $timelineContentList = $cachedTimeline
            ->setDateRange($startDateTime, $endDateTime)
            ->execute()
            ->getTimelineContentList();

        $previousDateTime = $this->getPreviousDateTime($startDateTime);
        $nextDateTime = $this->getNextDateTime($startDateTime);

        $previous = null;
        if ($previousDateTime >= $lowerLimitDateTime) {
            $previous = [
                'year' => (int) $previousDateTime->format('Y'),
                'month' => (int) $previousDateTime->format('m'),
            ];
        }

        $next = null;
        if ($nextDateTime <= new \DateTime()) {
            $next = [
                'year' => (int) $nextDateTime->format('Y'),
                'month' => (int) $nextDateTime->format('m'),
            ];
        }

        return new JsonResponse([
            'tabs' => $timelineContentList,
            'navigation' => [
                'previous' => $previous,
                'next' => $next,
            ],
            'period' => [
                'year' => $year,
                'month' => $month,
            ],
        ]);
    }

    private function getNextDateTime(\DateTime $dateTime): \DateTime
    {
        $nextDateTime = clone $dateTime;

        return $nextDateTime->add(new \DateInterval('P1M'));
    }

    private function getPreviousDateTime(\DateTime $dateTime): \DateTime
    {
        $previousDateTime = clone $dateTime;

        return $previousDateTime->sub(new \DateInterval('P1M'));
    }
}
