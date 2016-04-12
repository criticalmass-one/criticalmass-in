<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\RideEstimateType;
use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\RideType;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\LatLngListGenerator\TimeLatLngListGenerator;
use Caldera\Bundle\CriticalmassCoreBundle\Statistic\RideEstimate\RideEstimateService;
use Caldera\Bundle\CriticalmassCoreBundle\Timeline\Timeline;
use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Caldera\Bundle\CriticalmassModelBundle\Entity\RideEstimate;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Track;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TimelineController extends AbstractController
{
    public function yearmonthAction(Request $request, $year, $month)
    {
        $lowerLimitDateTime = new \DateTime('2010-01-01');

        $startDateTime = new \DateTime($year.'-'.$month.'-01');

        if ($startDateTime < $lowerLimitDateTime) {
            $startDateTime = new \DateTime();
        }

        $endDateTime = new \DateTime($year.'-'.$month.'-'.$startDateTime->format('t'));

        /**
         * @var Timeline $timeline
         */
        $timelineContent = $this
            ->get('caldera.criticalmass.timeline.cached')
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

        return $this->render(
            'CalderaCriticalmassSiteBundle:Timeline:yearmonth.html.twig',
            [
                'timelineContent' => $timelineContent,
                'startDateTime' => $startDateTime,
                'endDateTime' => $endDateTime,
                'nextDateTime' => $nextDateTime,
                'previousDateTime' => $previousDateTime
            ]
        );
    }

    public function indexAction(Request $request)
    {
        $dateTime = new \DateTime();

        return $this->yearmonthAction($request, $dateTime->format('Y'), $dateTime->format('M'));
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
}
