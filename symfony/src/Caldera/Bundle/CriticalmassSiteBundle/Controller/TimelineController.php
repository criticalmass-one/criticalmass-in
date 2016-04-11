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
        $startDateTime = new \DateTime($year.'-'.$month.'-01');
        $endDateTime = new \DateTime($year.'-'.$month.'-'.$startDateTime->format('t'));

        /**
         * @var Timeline $timeline
         */
        $timelineContent = $this
            ->get('caldera.criticalmass.timeline')
            ->setDateRange($startDateTime, $endDateTime)
            ->execute()
            ->getTimelineContent();

        return $this->render(
            'CalderaCriticalmassSiteBundle:Timeline:yearmonth.html.twig',
            [
                'timelineContent' => $timelineContent,
                'startDateTime' => $startDateTime,
                'endDateTime' => $endDateTime
            ]
        );
    }
}
