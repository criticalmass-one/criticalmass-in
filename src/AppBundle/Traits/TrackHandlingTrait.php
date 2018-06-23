<?php

namespace Criticalmass\Bundle\AppBundle\Traits;

use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Bundle\AppBundle\Entity\Track;
use Criticalmass\Bundle\AppBundle\Criticalmass\Gps\DistanceCalculator\TrackDistanceCalculator;
use Criticalmass\Bundle\AppBundle\Criticalmass\Gps\GpxReader\TrackReader;
use Criticalmass\Bundle\AppBundle\Criticalmass\Gps\LatLngListGenerator\RangeLatLngListGenerator;
use Criticalmass\Bundle\AppBundle\Criticalmass\Gps\LatLngListGenerator\SimpleLatLngListGenerator;
use Criticalmass\Bundle\AppBundle\Criticalmass\Gps\TrackPolyline\PolylineGenerator;
use Criticalmass\Bundle\AppBundle\Criticalmass\Statistic\RideEstimate\RideEstimateHandler;
use Criticalmass\Bundle\AppBundle\Criticalmass\Statistic\RideEstimate\RideEstimateService;

/** @deprecated  */
trait TrackHandlingTrait
{
    /** @deprecated  */
    protected function loadTrackProperties(Track $track)
    {
        /**
         * @var TrackReader $gr
         */
        $gr = $this->get('caldera.criticalmass.gps.trackreader');
        $gr->loadTrack($track);

        $track
            ->setPoints($gr->countPoints())
            ->setStartPoint(0)
            ->setEndPoint($gr->countPoints() - 1)
            ->setStartDateTime($gr->getStartDateTime())
            ->setEndDateTime($gr->getEndDateTime());

        /**
         * @var TrackDistanceCalculator $tdc
         */
        $tdc = $this->get('caldera.criticalmass.gps.distancecalculator.track');
        $tdc->loadTrack($track);

        $track->setDistance($tdc->calculate());

        $track->setMd5Hash($gr->getMd5Hash());
    }

    /** @deprecated  */
    protected function addRideEstimate(Track $track, Ride $ride)
    {
        $estimateService = $this->get(RideEstimateService::class);
        $estimateService
            ->addEstimateFromTrack($track)
            ->calculateEstimates($ride);
    }

    /** @deprecated  */
    protected function generateSimpleLatLngList(Track $track)
    {
        /**
         * @var SimpleLatLngListGenerator $generator
         */
        $generator = $this->get('caldera.criticalmass.gps.latlnglistgenerator.simple');
        $list = $generator
            ->loadTrack($track)
            ->execute()
            ->getList();

        $track->setLatLngList($list);

        $em = $this->getDoctrine()->getManager();
        $em->persist($track);
        $em->flush();
    }

    /** @deprecated  */
    protected function saveLatLngList(Track $track)
    {
        /**
         * @var RangeLatLngListGenerator $llag
         */
        $llag = $this->container->get('caldera.criticalmass.gps.latlnglistgenerator.range');
        $llag->loadTrack($track);
        $llag->execute();
        $track->setLatLngList($llag->getList());

        $em = $this->getDoctrine()->getManager();
        $em->persist($track);
        $em->flush();
    }

    /** @deprecated  */
    protected function updateTrackProperties(Track $track)
    {
        /**
         * @var TrackReader $tr
         */
        $tr = $this->get('caldera.criticalmass.gps.trackreader');
        $tr->loadTrack($track);

        $track->setStartDateTime($tr->getStartDateTime());
        $track->setEndDateTime($tr->getEndDateTime());
        $track->setDistance($tr->calculateDistance());

        $em = $this->getDoctrine()->getManager();
        $em->persist($track);
        $em->flush();
    }

    /** @deprecated  */
    protected function calculateRideEstimates(Track $track)
    {
        /** @var RideEstimateHandler $reh */
        $reh = $this->get(RideEstimateHandler::class);

        $reh
            ->setRide($track->getRide())
            ->flushEstimates()
            ->addEstimateFromTrack($track);
        $reh->calculateEstimates($track->getRide());
    }

    /** @deprecated  */
    protected function generatePolyline(Track $track)
    {
        /**
         * @var PolylineGenerator $trackPolyline
         */
        $trackPolyline = $this->get('caldera.criticalmass.gps.polyline.track');

        $trackPolyline->loadTrack($track);

        $trackPolyline->execute();

        $track->setPolyline($trackPolyline->getPolyline());

        $em = $this->getDoctrine()->getManager();
        $em->persist($track);
        $em->flush();
    }


}
