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
use Doctrine\Persistence\ManagerRegistry;
use League\Flysystem\FilesystemOperator;
use phpGPX\Models\GpxFile;
use phpGPX\Models\Segment;
use phpGPX\phpGPX;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TrackEventSubscriber implements EventSubscriberInterface
{
    protected TrackReader $trackReader;
    protected TrackPolylineHandlerInterface $trackPolylineHandler;
    protected RangeLatLngListGenerator $rangeLatLngListGenerator;
    protected RideEstimateHandlerInterface $rideEstimateHandler;
    protected TrackDistanceCalculatorInterface $trackDistanceCalculator;
    protected RideEstimateConverterInterface $rideEstimateConverter;
    protected ManagerRegistry $registry;
    protected ParticipationManagerInterface $participationManager;

    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
        ManagerRegistry $registry,
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
        $phpGpx = new PhpGpx();
        $track = $trackUploadedEvent->getTrack();

        $trackDirectory = $this->parameterBag->get('upload_destination.track');
        $filename = sprintf('%s/%s', $trackDirectory, $track->getTrackFilename());
        $gpxFile = $phpGpx->load($filename);

        $this->loadTrackProperties($track, $gpxFile);
        $this->addRideEstimate($track, $track->getRide());
        $this->updateLatLngList($track);
        $this->updatePolyline($track);

        dd($track);

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

        $this->calculateTrackDistance($track);

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

    protected function loadTrackProperties(Track $track, GpxFile $gpxFile): void
    {
        $gpxTrack = $gpxFile->tracks[0];
        $gpxStats = $gpxTrack->stats;
        $pointCounter = count($gpxTrack->getPoints());

        $track
            ->setPoints($pointCounter)
            ->setStartPoint(0)
            ->setEndPoint($pointCounter - 1)
            ->setStartDateTime($gpxStats->startedAt)
            ->setEndDateTime($gpxStats->finishedAt)
            ->setDistance($gpxStats->distance)
        ;
    }

    protected function calculateTrackDistance(Track $track): void
    {
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
            ->setEndDateTime($this->trackReader->getEndDateTime())
        ;
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

        if ($estimate) {
            $this->registry->getManager()->remove($estimate);
        }

        $this->registry->getManager()->flush();
    }
}
