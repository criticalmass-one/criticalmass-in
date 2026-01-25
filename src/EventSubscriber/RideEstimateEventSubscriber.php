<?php declare(strict_types=1);

namespace App\EventSubscriber;

use App\Criticalmass\Statistic\RideEstimateHandler\RideEstimateHandler;
use App\Criticalmass\Statistic\RideEstimateHandler\RideEstimateHandlerInterface;
use App\Event\RideEstimate\RideEstimateCreatedEvent;
use App\Event\RideEstimate\RideEstimateDeletedEvent;
use App\Event\RideEstimate\RideEstimateUpdatedEvent;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RideEstimateEventSubscriber implements EventSubscriberInterface
{
    protected RideEstimateHandlerInterface $rideEstimateHandler;
    protected ManagerRegistry $registry;

    public function __construct(
        ManagerRegistry $registry,
        RideEstimateHandler $rideEstimateHandler
    ) {
        $this->rideEstimateHandler = $rideEstimateHandler;
        $this->registry = $registry;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RideEstimateCreatedEvent::NAME => 'onRideEstimateCreated',
            RideEstimateUpdatedEvent::NAME => 'onRideEstimateUpdated',
            RideEstimateDeletedEvent::NAME => 'onRideEstimateDeleted',
        ];
    }

    public function onRideEstimateCreated(RideEstimateCreatedEvent $rideEstimateCreatedEvent): void
    {
        $rideEstimate = $rideEstimateCreatedEvent->getRideEstimate();
        $ride = $rideEstimate->getRide();

        $this->rideEstimateHandler
            ->setRide($ride)
            ->calculateEstimates();
    }

    public function onRideEstimateUpdated(RideEstimateUpdatedEvent $rideEstimateUpdatedEvent): void
    {
        $rideEstimate = $rideEstimateUpdatedEvent->getRideEstimate();
        $ride = $rideEstimate->getRide();

        $this->rideEstimateHandler
            ->setRide($ride)
            ->flushEstimates()
            ->calculateEstimates();
    }

    public function onRideEstimateDeleted(RideEstimateDeletedEvent $rideEstimateDeletedEvent): void
    {
        $rideEstimate = $rideEstimateDeletedEvent->getRideEstimate();
        $ride = $rideEstimate->getRide();

        $this->rideEstimateHandler
            ->setRide($ride)
            ->flushEstimates()
            ->calculateEstimates();
    }
}
