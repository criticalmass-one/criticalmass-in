<?php declare(strict_types=1);

namespace Tests\RideNamer;

use App\Criticalmass\RideNamer\CountingEnglishRideNamer;
use App\Criticalmass\RideNamer\CountingGermanRideNamer;
use App\Entity\City;
use App\Entity\Ride;
use App\Repository\RideRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CountingRideNamerTest extends TestCase
{
    private function registry(int $existingRides): ManagerRegistry
    {
        $repository = $this->createMock(RideRepository::class);
        $repository->method('countRidesByCity')->willReturn($existingRides);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getRepository')->with(Ride::class)->willReturn($repository);

        return $registry;
    }

    private function ride(): Ride
    {
        $city = (new City())->setCity('Hamburg');
        $city->setTitle('Critical Mass Hamburg');

        return (new Ride())->setCity($city);
    }

    /**
     * @return iterable<string, array{0: int, 1: string, 2: string}>
     */
    public static function counts(): iterable
    {
        yield 'first ride' => [0, '1. Critical Mass Hamburg', '1th Critical Mass Hamburg'];
        yield 'second ride' => [1, '2. Critical Mass Hamburg', '2th Critical Mass Hamburg'];
        yield 'hundredth ride' => [99, '100. Critical Mass Hamburg', '100th Critical Mass Hamburg'];
    }

    #[Test]
    #[DataProvider('counts')]
    public function titleIsTheNextOrdinalOfTheCityTitle(int $existingRides, string $german, string $english): void
    {
        $registry = $this->registry($existingRides);

        // The English namer always appends "th" (no 1st/2nd/3rd handling) — pinned here as-is.
        self::assertSame($german, (new CountingGermanRideNamer($registry))->generateTitle($this->ride()));
        self::assertSame($english, (new CountingEnglishRideNamer($registry))->generateTitle($this->ride()));
    }

    #[Test]
    public function countIsTakenFromTheRideCity(): void
    {
        $ride = $this->ride();

        $repository = $this->createMock(RideRepository::class);
        $repository->expects(self::once())->method('countRidesByCity')->with($ride->getCity())->willReturn(4);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getRepository')->willReturn($repository);

        self::assertSame('5. Critical Mass Hamburg', (new CountingGermanRideNamer($registry))->generateTitle($ride));
    }
}
