<?php declare(strict_types=1);

namespace Tests\EventSubscriber;

use App\Criticalmass\Statistic\RideEstimateHandler\RideEstimateHandler;
use App\Entity\Ride;
use App\Entity\RideEstimate;
use App\Event\RideEstimate\RideEstimateCreatedEvent;
use App\Event\RideEstimate\RideEstimateDeletedEvent;
use App\Event\RideEstimate\RideEstimateUpdatedEvent;
use App\EventSubscriber\RideEstimateEventSubscriber;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RideEstimateEventSubscriberTest extends TestCase
{
    private MockObject&RideEstimateHandler $handler;
    private RideEstimateEventSubscriber $subscriber;
    private RideEstimate $estimate;
    private Ride $ride;

    protected function setUp(): void
    {
        $this->ride = new Ride();
        $this->estimate = (new RideEstimate())->setRide($this->ride);

        $this->handler = $this->createMock(RideEstimateHandler::class);
        $this->handler->method('setRide')->willReturnSelf();
        $this->handler->method('flushEstimates')->willReturnSelf();
        $this->handler->method('calculateEstimates')->willReturnSelf();

        $this->subscriber = new RideEstimateEventSubscriber($this->createMock(ManagerRegistry::class), $this->handler);
    }

    #[Test]
    public function createdEstimateOnlyRecalculates(): void
    {
        $this->handler->expects(self::once())->method('setRide')->with($this->ride);
        $this->handler->expects(self::never())->method('flushEstimates');
        $this->handler->expects(self::once())->method('calculateEstimates');

        $this->subscriber->onRideEstimateCreated(new RideEstimateCreatedEvent($this->estimate));
    }

    #[Test]
    public function updatedEstimateFlushesBeforeRecalculating(): void
    {
        $this->handler->expects(self::once())->method('flushEstimates');
        $this->handler->expects(self::once())->method('calculateEstimates');

        $this->subscriber->onRideEstimateUpdated(new RideEstimateUpdatedEvent($this->estimate));
    }

    #[Test]
    public function deletedEstimateFlushesBeforeRecalculating(): void
    {
        $this->handler->expects(self::once())->method('flushEstimates');
        $this->handler->expects(self::once())->method('calculateEstimates');

        $this->subscriber->onRideEstimateDeleted(new RideEstimateDeletedEvent($this->estimate));
    }

    #[Test]
    public function subscribesToEstimateEvents(): void
    {
        self::assertSame([
            'ride_estimate.created' => 'onRideEstimateCreated',
            'ride_estimate.updated' => 'onRideEstimateUpdated',
            'ride_estimate.deleted' => 'onRideEstimateDeleted',
        ], RideEstimateEventSubscriber::getSubscribedEvents());
    }
}
