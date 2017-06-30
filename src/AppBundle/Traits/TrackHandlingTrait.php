<?php

namespace AppBundle\Traits;

use AppBundle\Entity\Ride;
use AppBundle\Entity\Track;
use AppBundle\Statistic\RideEstimate\RideEstimateService;
use Caldera\AppBundle\DistanceCalculator\TrackDistanceCalculator;
use Caldera\GeoBundle\GpxReader\TrackReader;

trait TrackHandlingTrait
{
    protected function getTrackReader(): TrackReader
    {
        return $this->get('caldera.geobundle.reader.track');
    }

    protected function getTrackDistanceCalculator(): TrackDistanceCalculator
    {
        return $this->get('caldera.geobundle.distance_calculator.track');
    }

    protected function loadTrackProperties(Track $track)
    {
        /**
         * @var TrackReader $gr
         */
        $gr = $this->getTrackReader();
        $gr->loadTrack($track);

        $track
            ->setPoints($gr->countPoints())
            ->setStartPoint(0)
            ->setEndPoint($gr->countPoints() - 1)
            ->setStartDateTime($gr->getStartDateTime())
            ->setEndDateTime($gr->getEndDateTime())
        ;

        $tdc = $this->getTrackDistanceCalculator();
        $tdc->loadTrack($track);

        $track->setDistance($tdc->calculate());
    }

    protected function addRideEstimate(Track $track, Ride $ride)
    {
        /**
         * @var RideEstimateService $estimateService
         */
        $estimateService = $this->get('caldera.criticalmass.statistic.rideestimate.track');
        $estimateService->addEstimate($track);
        $estimateService->calculateEstimates($ride);
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

    protected function updateTrackProperties(Track $track)
    {
        $tr = $this->getTrackReader();
        $tr->loadTrack($track);

        $track
            ->setStartDateTime($tr->getStartDateTime())
            ->setEndDateTime($tr->getEndDateTime())
        ;

        $tdc = $this->getTrackDistanceCalculator();
        $tdc->loadTrack($track);

        $track->setDistance($tdc->calculate());

        $this->getDoctrine()->getManager()->flush();
    }

    protected function calculateRideEstimates(Track $track)
    {
        /**
         * @var RideEstimateService $res
         */
        $res = $this->get('caldera.criticalmass.statistic.rideestimate.track');
        $res->flushEstimates($track->getRide());

        $res->refreshEstimate($track->getRideEstimate());
        $res->calculateEstimates($track->getRide());
    }

    /** @deprecated  */
    protected function generatePolyline(Track $track)
    {
        /**
         * @var TrackPolyline $trackPolyline
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
