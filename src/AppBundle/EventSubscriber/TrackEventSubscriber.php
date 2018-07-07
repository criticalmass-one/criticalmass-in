<?php declare(strict_types=1);

namespace AppBundle\EventSubscriber;

use AppBundle\Criticalmass\Gps\DistanceCalculator\TrackDistanceCalculatorInterface;
use AppBundle\Criticalmass\Statistic\RideEstimateHandler\RideEstimateHandlerInterface;
use AppBundle\Entity\Ride;
use AppBundle\Entity\Track;
use AppBundle\Event\Track\TrackDeletedEvent;
use AppBundle\Event\Track\TrackHiddenEvent;
use AppBundle\Event\Track\TrackShownEvent;
use AppBundle\Event\Track\TrackTimeEvent;
use AppBundle\Event\Track\TrackTrimmedEvent;
use AppBundle\Criticalmass\Gps\GpxReader\TrackReader;
use AppBundle\Criticalmass\Gps\LatLngListGenerator\RangeLatLngListGenerator;
use AppBundle\Criticalmass\Gps\TrackPolyline\PolylineGeneratorInterface;
use AppBundle\Event\Track\TrackUpdatedEvent;
use AppBundle\Event\Track\TrackUploadedEvent;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TrackEventSubscriber implements EventSubscriberInterface
{
    /** @var TrackReader $trackReader */
    protected $trackReader;

    /** @var PolylineGeneratorInterface $trackPolyline */
    protected $trackPolyline;

    /** @var RangeLatLngListGenerator $rangeLatLngListGenerator */
    protected $rangeLatLngListGenerator;

    /** @var RideEstimateHandlerInterface $rideEstimateHandler */
    protected $rideEstimateHandler;

    /** @var TrackDistanceCalculatorInterface $trackDistanceCalculator */
    protected $trackDistanceCalculator;

    /** @var Registry $registry */
    protected $registry;

    public function __construct(
        Registry $registry,
        RideEstimateHandlerInterface $rideEstimateHandler,
        TrackReader $trackReader,
        PolylineGeneratorInterface $trackPolyline,
        RangeLatLngListGenerator $rangeLatLngListGenerator,
        TrackDistanceCalculatorInterface $trackDistanceCalculator
    ) {
        $this->trackPolyline = $trackPolyline;

        $this->rangeLatLngListGenerator = $rangeLatLngListGenerator;

        $this->trackReader = $trackReader;

        $this->rideEstimateHandler = $rideEstimateHandler;

        $this->trackDistanceCalculator = $trackDistanceCalculator;

        $this->registry = $registry;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TrackDeletedEvent::NAME => 'onTrackDeleted',
            TrackHiddenEvent::NAME => 'onTrackHidden',
            TrackShownEvent::NAME => 'onTrackShown',
            TrackTimeEvent::NAME => 'onTrackTime',
            TrackTrimmedEvent::NAME => 'onTrackTrimmed',
            TrackUpdatedEvent::NAME => 'onTrackUpdated',
            TrackUploadedEvent::NAME => 'onTrackUploaded',
        ];
    }

    public function onTrackHidden(TrackHiddenEvent $trackHiddenEvent): void
    {
        $track = $trackHiddenEvent->getTrack();
        $this->removeEstimateFromTrack($track);

        $this->rideEstimateHandler
            ->setRide($track->getRide())
            ->flushEstimates()
            ->calculateEstimates();
    }

    public function onTrackShown(TrackShownEvent $trackShownEvent): void
    {
        $track = $trackShownEvent->getTrack();
        $ride = $track->getRide();

        $this->addRideEstimate($track, $ride);
    }

    public function onTrackDeleted(TrackDeletedEvent $trackDeletedEvent): void
    {
        $track = $trackDeletedEvent->getTrack();
        $this->removeEstimateFromTrack($track);

        $this->rideEstimateHandler
            ->setRide($track->getRide())
            ->flushEstimates()
            ->calculateEstimates();
    }

    public function onTrackTime(TrackTimeEvent $trackTimeEvent): void
    {
        $track = $trackTimeEvent->getTrack();

        $this->updateTrackProperties($track);

        $this->registry->getManager()->flush();
    }

    public function onTrackUploaded(TrackUploadedEvent $trackUploadedEvent): void
    {
        $track = $trackUploadedEvent->getTrack();

        $this->loadTrackProperties($track);
        $this->addRideEstimate($track, $track->getRide());
        $this->updateLatLngList($track);
        $this->updatePolyline($track);

        $this->registry->getManager()->flush();
    }

    public function onTrackTrimmed(TrackTrimmedEvent $trackTrimmedEvent): void
    {
        $track = $trackTrimmedEvent->getTrack();

        $this->removeEstimateFromTrack($track);
        
        $this->updatePolyline($track);

        $this->updateLatLngList($track);

        $this->updateTrackProperties($track);

        $this->updateEstimates($track);

        $this->registry->getManager()->flush();
    }

    protected function addRideEstimate(Track $track, Ride $ride)
    {
        $this->rideEstimateHandler
            ->setRide($ride)
            ->addEstimateFromTrack($track);

        $this->rideEstimateHandler->calculateEstimates();
    }

    protected function updatePolyline(Track $track): void
    {
        $polyline = $this->trackPolyline
            ->loadTrack($track)
            ->execute()
            ->getPolyline();

        $track->setPolyline($polyline);
    }

    protected function updateLatLngList(Track $track): void
    {
        $this->rangeLatLngListGenerator
            ->loadTrack($track)
            ->execute();

        $track->setLatLngList($this->rangeLatLngListGenerator->getList());
    }

    protected function loadTrackProperties(Track $track): void
    {
        $this->trackReader->loadTrack($track);

        $track
            ->setPoints($this->trackReader->countPoints())
            ->setStartPoint(0)
            ->setEndPoint($this->trackReader->countPoints() - 1)
            ->setStartDateTime($this->trackReader->getStartDateTime())
            ->setEndDateTime($this->trackReader->getEndDateTime());


        $distance = $this->trackDistanceCalculator
            ->loadTrack($track)
            ->calculate();

        $track->setDistance($distance);

        $track->setMd5Hash($this->trackReader->getMd5Hash());
    }

    protected function updateTrackProperties(Track $track): void
    {
        $this->trackReader->loadTrack($track);

        $track
            ->setStartDateTime($this->trackReader->getStartDateTime())
            ->setEndDateTime($this->trackReader->getEndDateTime())
            ->setDistance($this->trackReader->calculateDistance());
    }

    public function updateEstimates(Track $track): void
    {
        $this->rideEstimateHandler
            ->setRide($track->getRide())
            ->flushEstimates()
            ->addEstimateFromTrack($track);

        $this->rideEstimateHandler->calculateEstimates();
    }

    protected function removeEstimateFromTrack(Track $track): void
    {
        $estimate = $track->getRideEstimate();

        $track->setRideEstimate(null);

        $this->registry->getManager()->remove($estimate);
        $this->registry->getManager()->flush();
    }
}
