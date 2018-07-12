<?php

namespace AppBundle\Controller;

use AppBundle\Criticalmass\Timeline\CachedTimeline;
use Symfony\Component\HttpFoundation\Request;

class TimelineController extends AbstractController
{
    public function yearmonthAction(Request $request, $year, $month)
    {
        $lowerLimitDateTime = new \DateTime('2010-01-01');

        $startDateTime = new \DateTime($year . '-' . $month . '-01');

        if ($startDateTime < $lowerLimitDateTime) {
            $startDateTime = new \DateTime();
        }

        $endDateTime = new \DateTime($year . '-' . $month . '-' . $startDateTime->format('t'));

        $timelineContent = $this
            ->get(CachedTimeline::class)
            ->setDateRange($startDateTime, $endDateTime)
            ->execute()
            ->getTimelineContent();

        $nextDateTime = $this->getNextDateTime($startDateTime);
        $previousDateTime = $this->getPreviousDateTime($startDateTime);

        if ($nextDateTime > new \DateTime()) {
            $nextDateTime = null;
        }

        if ($previousDateTime < $lowerLimitDateTime) {
            $previousDateTime = null;
        }

        return $this->render('AppBundle:Timeline:yearmonth.html.twig', [
            'timelineContent' => $timelineContent,
            'startDateTime' => $startDateTime,
            'endDateTime' => $endDateTime,
            'nextDateTime' => $nextDateTime,
            'previousDateTime' => $previousDateTime
        ]);
    }

    protected function getNextDateTime(\DateTime $dateTime)
    {
        $nextDateTime = clone $dateTime;

        $dateInterval = new \DateInterval('P1M');

        return $nextDateTime->add($dateInterval);
    }

    protected function getPreviousDateTime(\DateTime $dateTime)
    {
        $previousDateTime = clone $dateTime;

        $dateInterval = new \DateInterval('P1M');

        return $previousDateTime->sub($dateInterval);
    }

    public function indexAction(Request $request)
    {
        $dateTime = new \DateTime();

        return $this->redirectToRoute('caldera_criticalmass_timeline_yearmonth', [
            'year' => $dateTime->format('Y'),
            'month' => $dateTime->format('m')
        ]);
    }
}
