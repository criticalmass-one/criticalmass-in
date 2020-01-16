<?php declare(strict_types=1);

namespace App\EventSubscriber;

use App\Criticalmass\Geo\DistanceCalculator\TrackDistanceCalculatorInterface;
use App\Criticalmass\Geo\GpxReader\TrackReader;
use App\Criticalmass\Geo\LatLngListGenerator\RangeLatLngListGenerator;
use App\Criticalmass\Geo\TrackPolylineHandler\TrackPolylineHandlerInterface;
use App\Criticalmass\Participation\Manager\ParticipationManagerInterface;
use App\Criticalmass\Statistic\RideEstimateConverter\RideEstimateConverterInterface;
use App\Criticalmass\Statistic\RideEstimateHandler\RideEstimateHandler;
use App\Criticalmass\Statistic\RideEstimateHandler\RideEstimateHandlerInterface;
use App\Entity\Ride;
use App\Entity\Track;
use App\Event\Track\TrackDeletedEvent;
use App\Event\Track\TrackHiddenEvent;
use App\Event\Track\TrackShownEvent;
use App\Event\Track\TrackTimeEvent;
use App\Event\Track\TrackTrimmedEvent;
use App\Event\Track\TrackUpdatedEvent;
use App\Event\Track\TrackUploadedEvent;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TrackEventSubscriber implements EventSubscriberInterface
{
    /** @var TrackReader $trackReader */
    protected $trackReader;

    /** @var TrackPolylineHandlerInterface $trackPolylineHandler */
    protected $trackPolylineHandler;

    /** @var RangeLatLngListGenerator $rangeLatLngListGenerator */
    protected $rangeLatLngListGenerator;

    /** @var RideEstimateHandlerInterface $rideEstimateHandler */
    protected $rideEstimateHandler;

    /** @var TrackDistanceCalculatorInterface $trackDistanceCalculator */
    protected $trackDistanceCalculator;

    /** @var RideEstimateConverterInterface $rideEstimateConverter */
    protected $rideEstimateConverter;

    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var ParticipationManagerInterface $participationManager */
    protected $participationManager;

    public function __construct(
        RegistryInterface $registry,
        RideEstimateHandler $rideEstimateHandler,
        RideEstimateConverterInterface $rideEstimateConverter,
        TrackReader $trackReader,
        RangeLatLngListGenerator $rangeLatLngListGenerator,
        TrackDistanceCalculatorInterface $trackDistanceCalculator,
        TrackPolylineHandlerInterface $trackPolylineHandler,
        ParticipationManagerInterface $participationManager
    ) {
        $this->rangeLatLngListGenerator = $rangeLatLngListGenerator;

        $this->trackReader = $trackReader;

        $this->rideEstimateHandler = $rideEstimateHandler;

        $this->trackDistanceCalculator = $trackDistanceCalculator;

        $this->rideEstimateConverter = $rideEstimateConverter;

        $this->registry = $registry;

        $this->trackPolylineHandler = $trackPolylineHandler;

        $this->participationManager = $participationManager;
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

        $this->participationManager->participate($track->getRide(), 'yes');
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
        $this->rideEstimateConverter->addEstimateFromTrack($track);

        $this->rideEstimateHandler
            ->setRide($ride)
            ->calculateEstimates();
    }

    protected function updatePolyline(Track $track): void
    {
         $this->trackPolylineHandler->handleTrack($track);
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
            ->setTrack($track)
            ->calculate();

        $track->setDistance($distance);
    }

    protected function updateTrackProperties(Track $track): void
    {
        $this->trackReader->loadTrack($track);

        $track
            ->setStartDateTime($this->trackReader->getStartDateTime())
            ->setEndDateTime($this->trackReader->getEndDateTime());
    }

    public function updateEstimates(Track $track): void
    {
        $this->rideEstimateConverter->addEstimateFromTrack($track);

        $this->rideEstimateHandler
            ->setRide($track->getRide())
            ->calculateEstimates();
    }

    protected function removeEstimateFromTrack(Track $track): void
    {
        $estimate = $track->getRideEstimate();

        $track->setRideEstimate(null);

        $this->registry->getManager()->remove($estimate);
        $this->registry->getManager()->flush();
    }
}
