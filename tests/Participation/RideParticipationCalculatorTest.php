<?php declare(strict_types=1);

namespace Tests\Participation;

use App\Criticalmass\Participation\Calculator\RideParticipationCalculator;
use App\Entity\Participation;
use App\Entity\Ride;
use App\Repository\ParticipationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RideParticipationCalculatorTest extends TestCase
{
    #[Test]
    public function storesCountsPerStatusOnTheRideAndFlushes(): void
    {
        $ride = new Ride();

        $repository = $this->createMock(ParticipationRepository::class);
        $repository->method('countParticipationsForRide')->willReturnCallback(
            static fn (Ride $r, string $status): int => match ($status) {
                'yes' => 12,
                'maybe' => 3,
                'no' => 1,
                default => throw new \LogicException('unexpected status ' . $status),
            }
        );

        $manager = $this->createMock(ObjectManager::class);
        $manager->expects(self::once())->method('flush');

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getRepository')->with(Participation::class)->willReturn($repository);
        $registry->method('getManager')->willReturn($manager);

        $calculator = (new RideParticipationCalculator($registry))->setRide($ride);

        self::assertSame($calculator, $calculator->calculate());
        self::assertSame(12, $ride->getParticipationsNumberYes());
        self::assertSame(3, $ride->getParticipationsNumberMaybe());
        self::assertSame(1, $ride->getParticipationsNumberNo());
    }
}
