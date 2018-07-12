<?php declare(strict_types=1);

namespace AppBundle\EventSubscriber;

use AppBundle\Criticalmass\Statistic\RideEstimateHandler\RideEstimateHandler;
use AppBundle\Criticalmass\Statistic\RideEstimateHandler\RideEstimateHandlerInterface;
use AppBundle\Event\RideEstimate\RideEstimateCreatedEvent;
use AppBundle\Event\RideEstimate\RideEstimateDeletedEvent;
use AppBundle\Event\RideEstimate\RideEstimateUpdatedEvent;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RideEstimateEventSubscriber implements EventSubscriberInterface
{
    /** @var RideEstimateHandlerInterface $rideEstimateHandler */
    protected $rideEstimateHandler;

    /** @var Registry $registry */
    protected $registry;

    public function __construct(
        Registry $registry,
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
