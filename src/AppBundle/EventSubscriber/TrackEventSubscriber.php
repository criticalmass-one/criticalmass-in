<?php declare(strict_types=1);

namespace AppBundle\EventSubscriber;

use AppBundle\Entity\Track;
use AppBundle\Event\Track\TrackTrimmedEvent;
use AppBundle\Criticalmass\Gps\GpxReader\TrackReader;
use AppBundle\Criticalmass\Gps\LatLngListGenerator\RangeLatLngListGenerator;
use AppBundle\Criticalmass\Gps\TrackPolyline\PolylineGeneratorInterface;
use AppBundle\Criticalmass\Statistic\RideEstimate\RideEstimateHandler;
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

    /** @var RideEstimateHandler $rideEstimateHandler */
    protected $rideEstimateHandler;

    /** @var Registry $registry */
    protected $registry;

    public function __construct(Registry $registry, RideEstimateHandler $rideEstimateHandler, TrackReader $trackReader, PolylineGeneratorInterface $trackPolyline, RangeLatLngListGenerator $rangeLatLngListGenerator)
    {
        $this->trackPolyline = $trackPolyline;

        $this->rangeLatLngListGenerator = $rangeLatLngListGenerator;

        $this->trackReader = $trackReader;

        $this->rideEstimateHandler = $rideEstimateHandler;

        $this->registry = $registry;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TrackTrimmedEvent::NAME => 'onTrackTrimmed',
        ];
    }

    public function onTrackTrimmed(TrackTrimmedEvent $trackTrimmedEvent): void
    {
        $track = $trackTrimmedEvent->getTrack();

        $this->updatePolyline($track);

        $this->updateLatLngList($track);

        $this->updateTrackProperties($track);

        $this->updateEstimates($track);

        $this->registry->getManager()->flush();
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
}
