<?php declare(strict_types=1);

namespace App\EventSubscriber;

use App\Event\Participation\ParticipationCreatedEvent;
use App\Event\Participation\ParticipationDeletedEvent;
use App\Event\Participation\ParticipationUpdatedEvent;
use App\Criticalmass\Participation\Calculator\RideParticipationCalculatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ParticipationEventSubscriber implements EventSubscriberInterface
{
    public function __construct(protected RideParticipationCalculatorInterface $rideParticipationCalculator)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ParticipationCreatedEvent::NAME => 'onParticipationCreated',
            ParticipationUpdatedEvent::NAME => 'onParticipationUpdated',
            ParticipationDeletedEvent::NAME => 'onParticipationDeleted',
        ];
    }

    public function onParticipationCreated(ParticipationCreatedEvent $participationCreatedEvent): void
    {
        $this->rideParticipationCalculator
            ->setRide($participationCreatedEvent->getParticipation()->getRide())
            ->calculate();
    }

    public function onParticipationUpdated(ParticipationUpdatedEvent $participationUpdatedEvent): void
    {
        $this->rideParticipationCalculator
            ->setRide($participationUpdatedEvent->getParticipation()->getRide())
            ->calculate();
    }

    public function onParticipationDeleted(ParticipationDeletedEvent $participationDeletedEvent): void
    {
        $this->rideParticipationCalculator
            ->setRide($participationDeletedEvent->getParticipation()->getRide())
            ->calculate();
    }
}
