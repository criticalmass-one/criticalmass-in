<?php declare(strict_types=1);

namespace Tests\EventSubscriber;

use App\Criticalmass\Participation\Calculator\RideParticipationCalculator;
use App\Entity\Participation;
use App\Entity\Ride;
use App\Event\Participation\ParticipationCreatedEvent;
use App\Event\Participation\ParticipationDeletedEvent;
use App\Event\Participation\ParticipationUpdatedEvent;
use App\EventSubscriber\ParticipationEventSubscriber;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ParticipationEventSubscriberTest extends TestCase
{
    #[Test]
    public function listensToAllThreeParticipationEvents(): void
    {
        self::assertSame([
            'participation.created' => 'onParticipationCreated',
            'participation.updated' => 'onParticipationUpdated',
            'participation.deleted' => 'onParticipationDeleted',
        ], ParticipationEventSubscriber::getSubscribedEvents());
    }

    #[Test]
    public function everyEventRecalculatesTheRideOfTheParticipation(): void
    {
        $ride = new Ride();
        $participation = (new Participation())->setRide($ride);

        $calculator = $this->createMock(RideParticipationCalculator::class);
        $calculator->expects(self::exactly(3))->method('setRide')->with($ride)->willReturnSelf();
        $calculator->expects(self::exactly(3))->method('calculate')->willReturnSelf();

        $subscriber = new ParticipationEventSubscriber($calculator);
        $subscriber->onParticipationCreated(new ParticipationCreatedEvent($participation));
        $subscriber->onParticipationUpdated(new ParticipationUpdatedEvent($participation));
        $subscriber->onParticipationDeleted(new ParticipationDeletedEvent($participation));
    }
}
