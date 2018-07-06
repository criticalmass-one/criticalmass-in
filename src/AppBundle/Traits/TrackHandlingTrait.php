<?php

namespace AppBundle\Traits;

use AppBundle\Entity\Ride;
use AppBundle\Entity\Track;
use AppBundle\Statistic\RideEstimate\RideEstimateService;
use Caldera\GeoBundle\DistanceCalculator\TrackDistanceCalculator;
use Caldera\GeoBundle\GpxReader\TrackReader;
use Caldera\GeoBundle\LatLngListGenerator\RangeLatLngListGenerator;
use Caldera\GeoBundle\LatLngListGenerator\SimpleLatLngListGenerator;
use Caldera\GeoBundle\PolylineGenerator\PolylineGenerator;
use AppBundle\Criticalmass\Statistic\RideEstimate\RideEstimateHandler;

/** @deprecated  */
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

    /** @deprecated  */
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
        $generator = $this->get('caldera.geobundle.latlnglist_generator.simple');
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
        $llag = $this->container->get('caldera.geobundle.latlnglist_generator.range');
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
        $olylineGeneator = $this->get('caldera.geobundle.polyline_generator');

        $olylineGeneator
            ->setTrack($track)
            ->processTrack()
        ;

        $this->getDoctrine()->getManager()->flush();
    }
}
