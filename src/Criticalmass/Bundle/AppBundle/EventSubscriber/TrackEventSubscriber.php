<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\EventSubscriber;

use Criticalmass\Bundle\AppBundle\Entity\Track;
use Criticalmass\Bundle\AppBundle\Event\Track\TrackTrimmedEvent;
use Criticalmass\Component\Gps\GpxReader\TrackReader;
use Criticalmass\Component\Gps\LatLngListGenerator\RangeLatLngListGenerator;
use Criticalmass\Component\Gps\TrackPolyline\TrackPolyline;
use Criticalmass\Component\Statistic\RideEstimate\RideEstimateHandler;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TrackEventSubscriber implements EventSubscriberInterface
{
    /** @var TrackReader $trackReader */
    protected $trackReader;

    /** @var TrackPolyline $trackPolyline */
    protected $trackPolyline;

    /** @var RangeLatLngListGenerator $rangeLatLngListGenerator */
    protected $rangeLatLngListGenerator;

    /** @var RideEstimateHandler $rideEstimateHandler */
    protected $rideEstimateHandler;

    /** @var Registry $registry */
    protected $registry;

    public function __construct(Registry $registry, RideEstimateHandler $rideEstimateHandler, TrackReader $trackReader, TrackPolyline $trackPolyline, RangeLatLngListGenerator $rangeLatLngListGenerator)
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
    }

    protected function updatePolyline(Track $track): void
    {
        $this->trackPolyline
            ->loadTrack($track)
            ->execute();

        $track->setPolyline($this->trackPolyline->getPolyline());
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

        $this->registry->getManager()->flush();
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
