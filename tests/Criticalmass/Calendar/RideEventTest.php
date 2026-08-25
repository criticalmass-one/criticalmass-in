<?php declare(strict_types=1);

namespace Tests\Criticalmass\Calendar;

use App\Criticalmass\Calendar\Event\RideEvent;
use App\Criticalmass\Calendar\EventProvider\RideProvider;
use App\Entity\Ride;
use App\Repository\RideRepository;
use CalendR\Event\EventInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Support\EntityIdHelper;

final class RideEventTest extends TestCase
{
    private function ride(int $id, string $dateTime = '2024-05-31 19:00:00'): Ride
    {
        $ride = (new Ride())->setDateTime(new \DateTime($dateTime));
        EntityIdHelper::setId($ride, $id);

        return $ride;
    }

    #[Test]
    public function eventIsAPointInTimeAtTheRideStart(): void
    {
        $ride = $this->ride(5);
        $event = new RideEvent($ride);

        self::assertSame($ride, $event->getRide());
        self::assertSame('ride-5', $event->getUid());
        self::assertEquals(new \DateTime('2024-05-31 19:00:00'), $event->getBegin());
        self::assertEquals($event->getBegin(), $event->getEnd());
    }

    #[Test]
    public function eventsAreEqualWhenRideAndTimesMatch(): void
    {
        $a = new RideEvent($this->ride(5));
        $b = new RideEvent($this->ride(5));

        self::assertTrue($a->isEqualTo($b));
    }

    #[Test]
    public function eventsOfDifferentRidesOrTimesAreNotEqual(): void
    {
        $a = new RideEvent($this->ride(5));

        self::assertFalse($a->isEqualTo(new RideEvent($this->ride(6))));
        self::assertFalse($a->isEqualTo(new RideEvent($this->ride(5, '2024-06-28 19:00:00'))));
        self::assertFalse($a->isEqualTo($this->createMock(EventInterface::class)));
    }

    #[Test]
    public function providerWrapsEveryRideInTheIntervalIntoAnEvent(): void
    {
        $begin = new \DateTimeImmutable('2024-05-01');
        $end = new \DateTimeImmutable('2024-05-31');
        $rides = [$this->ride(1), $this->ride(2)];

        $repository = $this->createMock(RideRepository::class);
        $repository->expects(self::once())->method('findRides')->with($begin, $end)->willReturn($rides);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getRepository')->with(Ride::class)->willReturn($repository);

        $events = (new RideProvider($registry))->getEvents($begin, $end);

        self::assertCount(2, $events);
        self::assertContainsOnlyInstancesOf(RideEvent::class, $events);
        self::assertSame(['ride-1', 'ride-2'], array_map(static fn (RideEvent $e): string => $e->getUid(), $events));
    }
}
