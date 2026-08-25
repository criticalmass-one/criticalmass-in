<?php declare(strict_types=1);

namespace Tests\Twig;

use App\Entity\Ride;
use App\Repository\RideRepository;
use App\Twig\Extension\RideNavigationTwigExtension;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RideNavigationTwigExtensionTest extends TestCase
{
    #[Test]
    public function previousAndNextDelegateToTheRepository(): void
    {
        $ride = new Ride();
        $previous = new Ride();
        $next = new Ride();

        $repository = $this->createMock(RideRepository::class);
        $repository->method('getPreviousRide')->with($ride)->willReturn($previous);
        $repository->method('getNextRide')->with($ride)->willReturn($next);

        $extension = new RideNavigationTwigExtension($repository);

        self::assertSame($previous, $extension->previousRide($ride));
        self::assertSame($next, $extension->nextRide($ride));
    }

    #[Test]
    public function missingNeighboursAreNull(): void
    {
        $repository = $this->createMock(RideRepository::class);
        $repository->method('getPreviousRide')->willReturn(null);
        $repository->method('getNextRide')->willReturn(null);

        $extension = new RideNavigationTwigExtension($repository);

        self::assertNull($extension->previousRide(new Ride()));
        self::assertNull($extension->nextRide(new Ride()));
    }

    #[Test]
    public function exposesPreviousAndNextRideFunctions(): void
    {
        $names = array_map(
            static fn (\Twig\TwigFunction $function): string => $function->getName(),
            (new RideNavigationTwigExtension($this->createMock(RideRepository::class)))->getFunctions()
        );

        self::assertSame(['previous_ride', 'next_ride'], $names);
    }
}
