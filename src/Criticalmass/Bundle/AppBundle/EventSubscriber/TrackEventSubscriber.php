<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\EventSubscriber;

use Criticalmass\Bundle\AppBundle\Event\Track\TrackTrimmedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TrackEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            TrackTrimmedEvent::NAME => 'onTrackTrimmed',
        ];
    }

    public function onTrackTrimmed(TrackTrimmedEvent $trackTrimmedEvent): void
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

        /** @var RideEstimateHandler $reh */
        $reh = $this->get(RideEstimateHandler::class);

        $reh
            ->setRide($track->getRide())
            ->flushEstimates()
            ->addEstimateFromTrack($track);
        $reh->calculateEstimates($track->getRide());
    }
}
