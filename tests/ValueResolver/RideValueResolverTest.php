<?php declare(strict_types=1);

namespace Tests\ValueResolver;

use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\Ride;
use App\Repository\CitySlugRepository;
use App\Repository\RideRepository;
use App\ValueResolver\RideValueResolver;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class RideValueResolverTest extends TestCase
{
    private MockObject&RideRepository $rideRepository;
    private City $city;
    private RideValueResolver $resolver;

    protected function setUp(): void
    {
        $this->city = new City();
        $citySlug = (new CitySlug())->setSlug('hamburg')->setCity($this->city);
        $this->city->setMainSlug($citySlug);

        $citySlugRepository = $this->createMock(CitySlugRepository::class);
        $citySlugRepository->method('findOneBy')->willReturnCallback(
            fn (array $criteria): ?CitySlug => 'hamburg' === ($criteria['slug'] ?? null) ? $citySlug : null
        );

        $this->rideRepository = $this->createMock(RideRepository::class);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getRepository')->willReturnMap([
            [CitySlug::class, null, $citySlugRepository],
            [Ride::class, null, $this->rideRepository],
        ]);

        $this->resolver = new RideValueResolver($registry);
    }

    private function argument(bool $nullable = false, string $name = 'ride', string $type = Ride::class): ArgumentMetadata
    {
        return new ArgumentMetadata($name, $type, false, false, null, $nullable);
    }

    #[Test]
    public function ignoresArgumentsOfOtherTypeOrName(): void
    {
        $request = Request::create('/?rideId=1');

        self::assertSame([], $this->resolver->resolve($request, $this->argument(name: 'other')));
        self::assertSame([], $this->resolver->resolve($request, $this->argument(type: City::class)));
    }

    #[Test]
    public function resolvesByRideIdFromQueryParameter(): void
    {
        $ride = new Ride();
        $this->rideRepository->expects(self::once())->method('find')->with('42')->willReturn($ride);

        $request = Request::create('/api/ride?rideId=42');

        self::assertSame([$ride], $this->resolver->resolve($request, $this->argument()));
    }

    #[Test]
    public function rideIdTakesPrecedenceOverSlugs(): void
    {
        $ride = new Ride();
        $this->rideRepository->method('find')->willReturn($ride);
        $this->rideRepository->expects(self::never())->method('findCityRideByDate');

        $request = Request::create('/?rideId=7&citySlug=hamburg&rideIdentifier=2024-05-31');

        self::assertSame([$ride], $this->resolver->resolve($request, $this->argument()));
    }

    #[Test]
    public function resolvesFullDateIdentifierViaCityRideByDate(): void
    {
        $ride = new Ride();
        $this->rideRepository->expects(self::once())
            ->method('findCityRideByDate')
            ->with($this->city, self::callback(fn (\DateTimeInterface $d): bool => '2024-05-31' === $d->format('Y-m-d')))
            ->willReturn($ride);

        $request = Request::create('/hamburg/2024-05-31');
        $request->attributes->set('citySlug', 'hamburg');
        $request->attributes->set('rideIdentifier', '2024-05-31');

        self::assertSame([$ride], $this->resolver->resolve($request, $this->argument()));
    }

    #[Test]
    public function resolvesMonthIdentifierViaFirstRideOfMonth(): void
    {
        $first = new Ride();
        $second = new Ride();
        $this->rideRepository->expects(self::once())
            ->method('findByCityAndMonth')
            ->with($this->city, self::callback(fn (\DateTimeInterface $d): bool => '2024-05-01' === $d->format('Y-m-d')))
            ->willReturn([$first, $second]);

        $request = Request::create('/?citySlug=hamburg&rideIdentifier=2024-05');

        self::assertSame([$first], $this->resolver->resolve($request, $this->argument()));
    }

    #[Test]
    public function trailingDashIsStrippedFromIdentifier(): void
    {
        $ride = new Ride();
        $this->rideRepository->method('findCityRideByDate')->willReturn($ride);

        $request = Request::create('/?citySlug=hamburg&rideIdentifier=2024-05-31-');

        self::assertSame([$ride], $this->resolver->resolve($request, $this->argument()));
    }

    #[Test]
    public function nonDateIdentifierIsLookedUpAsSlug(): void
    {
        $ride = new Ride();
        $this->rideRepository->expects(self::once())
            ->method('findOneByCityAndSlug')
            ->with($this->city, 'kidical-mass')
            ->willReturn($ride);

        $request = Request::create('/?citySlug=hamburg&rideIdentifier=kidical-mass');

        self::assertSame([$ride], $this->resolver->resolve($request, $this->argument()));
    }

    #[Test]
    public function unknownCitySlugThrowsNotFound(): void
    {
        $request = Request::create('/?citySlug=atlantis&rideIdentifier=2024-05-31');

        $this->expectException(NotFoundHttpException::class);

        $this->resolver->resolve($request, $this->argument());
    }

    #[Test]
    public function missingParametersYieldNullForNullableArgument(): void
    {
        $request = Request::create('/');

        self::assertSame([null], $this->resolver->resolve($request, $this->argument(nullable: true)));
    }

    #[Test]
    public function missingParametersThrowForRequiredArgument(): void
    {
        $request = Request::create('/?citySlug=hamburg');

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Ride not found');

        $this->resolver->resolve($request, $this->argument());
    }
}
