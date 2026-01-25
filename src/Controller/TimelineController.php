<?php declare(strict_types=1);

namespace App\Controller;

use App\Criticalmass\Timeline\TimelineInterface;
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
        $lowerLimitDateTime = new \DateTime('2010-01-01');

        $startDateTime = new \DateTime($year . '-' . $month . '-01');

        if ($startDateTime < $lowerLimitDateTime) {
            $startDateTime = new \DateTime();
        }

        $endDateTime = new \DateTime($year . '-' . $month . '-' . $startDateTime->format('t'));

        $timelineContentList = $cachedTimeline
            ->setDateRange($startDateTime, $endDateTime)
            ->execute()
            ->getTimelineContentList();

        $nextDateTime = $this->getNextDateTime($startDateTime);
        $previousDateTime = $this->getPreviousDateTime($startDateTime);

        if ($nextDateTime > new \DateTime()) {
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

    protected function getNextDateTime(\DateTime $dateTime): \DateTime
    {
        $nextDateTime = clone $dateTime;
        $dateInterval = new \DateInterval('P1M');
        return $nextDateTime->add($dateInterval);
    }

    protected function getPreviousDateTime(\DateTime $dateTime): \DateTime
    {
        $previousDateTime = clone $dateTime;
        $dateInterval = new \DateInterval('P1M');
        return $previousDateTime->sub($dateInterval);
    }

    #[Route(
        '/timeline',
        name: 'caldera_criticalmass_timeline_index',
        priority: 130
    )]
    public function indexAction(): RedirectResponse
    {
        $dateTime = new \DateTime();

        return $this->redirectToRoute('caldera_criticalmass_timeline_yearmonth', [
            'year' => $dateTime->format('Y'),
            'month' => $dateTime->format('m'),
        ]);
    }
}
