<?php declare(strict_types=1);

namespace App\Controller;

use App\Criticalmass\Timeline\TimelineInterface;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TimelineController extends AbstractController
{
    #[Route(
        '/timeline/{year}/{month}',
        name: 'caldera_criticalmass_timeline_yearmonth',
        requirements: ['year' => '([0-9]{4,4})', 'month' => '([0-9]{2,2})'],
        priority: 130
    )]
    public function yearmonthAction(TimelineInterface $cachedTimeline, int $year, int $month): Response
    {
        $lowerLimitDateTime = Carbon::parse('2010-01-01');

        $startDateTime = Carbon::parse($year . '-' . $month . '-01');

        if ($startDateTime < $lowerLimitDateTime) {
            $startDateTime = Carbon::now();
        }

        $endDateTime = Carbon::parse($year . '-' . $month . '-' . $startDateTime->format('t'));

        $timelineContentList = $cachedTimeline
            ->setDateRange($startDateTime, $endDateTime)
            ->execute()
            ->getTimelineContentList();

        $nextDateTime = $this->getNextDateTime($startDateTime);
        $previousDateTime = $this->getPreviousDateTime($startDateTime);

        if ($nextDateTime > Carbon::now()) {
            $nextDateTime = null;
        }

        if ($previousDateTime < $lowerLimitDateTime) {
            $previousDateTime = null;
        }

        return $this->render('Timeline/yearmonth.html.twig', [
            'timelineContentList' => $timelineContentList,
            'startDateTime' => $startDateTime,
            'endDateTime' => $endDateTime,
            'nextDateTime' => $nextDateTime,
            'previousDateTime' => $previousDateTime,
        ]);
    }

    protected function getNextDateTime(Carbon $dateTime): Carbon
    {
        return (clone $dateTime)->addMonth();
    }

    protected function getPreviousDateTime(Carbon $dateTime): Carbon
    {
        return (clone $dateTime)->subMonth();
    }

    #[Route(
        '/timeline',
        name: 'caldera_criticalmass_timeline_index',
        priority: 130
    )]
    public function indexAction(): RedirectResponse
    {
        $dateTime = Carbon::now();

        return $this->redirectToRoute('caldera_criticalmass_timeline_yearmonth', [
            'year' => $dateTime->format('Y'),
            'month' => $dateTime->format('m'),
        ]);
    }
}
