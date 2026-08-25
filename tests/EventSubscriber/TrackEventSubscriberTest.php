<?php declare(strict_types=1);

namespace Tests\EventSubscriber;

use App\Criticalmass\Geo\GpxService\GpxServiceInterface;
use App\Criticalmass\Participation\Manager\ParticipationManagerInterface;
use App\Criticalmass\Statistic\RideEstimateConverter\RideEstimateConverterInterface;
use App\Criticalmass\Statistic\RideEstimateHandler\RideEstimateHandler;
use App\Entity\Ride;
use App\Entity\RideEstimate;
use App\Entity\Track;
use App\Event\Track\TrackDeletedEvent;
use App\Event\Track\TrackHiddenEvent;
use App\Event\Track\TrackShownEvent;
use App\Event\Track\TrackTimeEvent;
use App\EventSubscriber\TrackEventSubscriber;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class TrackEventSubscriberTest extends TestCase
{
    private MockObject&GpxServiceInterface $gpxService;
    private MockObject&ObjectManager $manager;
    private MockObject&RideEstimateHandler $estimateHandler;
    private MockObject&RideEstimateConverterInterface $estimateConverter;
    private MockObject&ParticipationManagerInterface $participationManager;
    private TrackEventSubscriber $subscriber;
    private Ride $ride;

    protected function setUp(): void
    {
        $this->ride = new Ride();

        $this->gpxService = $this->createMock(GpxServiceInterface::class);
        $this->manager = $this->createMock(ObjectManager::class);
        $this->estimateHandler = $this->createMock(RideEstimateHandler::class);
        $this->estimateHandler->method('setRide')->willReturnSelf();
        $this->estimateHandler->method('flushEstimates')->willReturnSelf();
        $this->estimateHandler->method('calculateEstimates')->willReturnSelf();
        $this->estimateConverter = $this->createMock(RideEstimateConverterInterface::class);
        $this->participationManager = $this->createMock(ParticipationManagerInterface::class);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getManager')->willReturn($this->manager);

        $this->subscriber = new TrackEventSubscriber(
            $this->gpxService,
            $registry,
            $this->estimateHandler,
            $this->estimateConverter,
            $this->participationManager,
        );
    }

    #[Test]
    public function hidingATrackRemovesItsEstimateAndRecalculatesTheRide(): void
    {
        $estimate = new RideEstimate();
        $track = (new Track())->setRide($this->ride)->setRideEstimate($estimate);

        $this->manager->expects(self::once())->method('remove')->with($estimate);
        $this->manager->expects(self::atLeastOnce())->method('flush');
        $this->estimateHandler->expects(self::once())->method('setRide')->with($this->ride);
        $this->estimateHandler->expects(self::once())->method('flushEstimates');
        $this->estimateHandler->expects(self::once())->method('calculateEstimates');

        $this->subscriber->onTrackHidden(new TrackHiddenEvent($track));

        self::assertNull($track->getRideEstimate());
    }

    #[Test]
    public function deletingATrackWithoutEstimateDoesNotRemoveAnything(): void
    {
        $track = (new Track())->setRide($this->ride);

        $this->manager->expects(self::never())->method('remove');
        $this->estimateHandler->expects(self::once())->method('calculateEstimates');

        $this->subscriber->onTrackDeleted(new TrackDeletedEvent($track));
    }

    #[Test]
    public function showingATrackAddsAnEstimateFromIt(): void
    {
        $track = (new Track())->setRide($this->ride);

        $this->estimateConverter->expects(self::once())->method('addEstimateFromTrack')->with($track)->willReturnSelf();
        $this->estimateHandler->expects(self::once())->method('setRide')->with($this->ride);
        $this->estimateHandler->expects(self::never())->method('flushEstimates');
        $this->estimateHandler->expects(self::once())->method('calculateEstimates');

        $this->subscriber->onTrackShown(new TrackShownEvent($track));
    }

    #[Test]
    public function timeChangeUpdatesStartAndEndFromGpx(): void
    {
        $track = (new Track())->setRide($this->ride);
        $start = new \DateTime('2024-05-31 19:00:00');
        $end = new \DateTime('2024-05-31 21:00:00');

        $this->gpxService->method('getStartDateTime')->with($track)->willReturn($start);
        $this->gpxService->method('getEndDateTime')->with($track)->willReturn($end);
        $this->manager->expects(self::once())->method('flush');

        $this->subscriber->onTrackTime(new TrackTimeEvent($track));

        self::assertEquals($start, $track->getStartDateTime());
        self::assertEquals($end, $track->getEndDateTime());
    }

    #[Test]
    public function timeChangeKeepsExistingValuesWhenGpxHasNone(): void
    {
        $start = new \DateTime('2024-05-31 19:00:00');
        $track = (new Track())->setRide($this->ride);
        $track->setStartDateTime($start);

        $this->gpxService->method('getStartDateTime')->willReturn(null);
        $this->gpxService->method('getEndDateTime')->willReturn(null);

        $this->subscriber->onTrackTime(new TrackTimeEvent($track));

        self::assertSame($start, $track->getStartDateTime());
        self::assertNull($track->getEndDateTime());
    }

    #[Test]
    public function subscribesToAllTrackEvents(): void
    {
        $events = TrackEventSubscriber::getSubscribedEvents();

        self::assertSame([
            'track.deleted', 'track.hidden', 'track.shown', 'track.time', 'track.trimmed', 'track.updated', 'track.uploaded',
        ], array_keys($events));
    }
}
