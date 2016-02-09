<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\RideEstimateType;
use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\RideType;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\LatLngListGenerator\TimeLatLngListGenerator;
use Caldera\Bundle\CriticalmassCoreBundle\Statistic\RideEstimate\RideEstimateService;
use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Caldera\Bundle\CriticalmassModelBundle\Entity\RideEstimate;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Track;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TimelapseController extends AbstractController
{
    public function showAction(Request $request, $citySlug, $rideDate)
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        $tracks = $this->getTrackRepository()->findTracksByRide($ride);

        return $this->render(
            'CalderaCriticalmassSiteBundle:Timelapse:show.html.twig',
            array(
                'ride' => $ride,
                'tracks' => $tracks
            )
        );
    }

    public function loadtrackAction(Request $request, $citySlug, $rideDate, $trackId)
    {
        /**
         * @var Track $track
         */
        $track = $this->getTrackRepository()->find($trackId);

        /**
         * @var TimeLatLngListGenerator $generator
         */
        $generator = $this->get('caldera.criticalmass.gps.latlnglistgenerator.time');

        $generator->loadTrack($track);

        $generator->execute();

        $list = $generator->getList();

        return new Response($list, 200, ['Content-Type' => 'text/json']);
    }
}
