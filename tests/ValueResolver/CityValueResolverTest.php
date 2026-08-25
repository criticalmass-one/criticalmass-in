<?php declare(strict_types=1);

namespace Tests\ValueResolver;

use App\Entity\City;
use App\Entity\CitySlug;
use App\Repository\CitySlugRepository;
use App\ValueResolver\CityValueResolver;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class CityValueResolverTest extends TestCase
{
    /** @var array<string, CitySlug> */
    private array $slugs = [];

    private function resolver(): CityValueResolver
    {
        $repository = $this->createMock(CitySlugRepository::class);
        $repository->method('__call')->willReturnCallback(
            fn (string $method, array $arguments): ?CitySlug => 'findOneBySlug' === $method ? ($this->slugs[$arguments[0]] ?? null) : null
        );

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getRepository')->with(CitySlug::class)->willReturn($repository);

        return new CityValueResolver($registry);
    }

    private function registerCity(string $slug): City
    {
        $city = new City();
        $citySlug = (new CitySlug())->setSlug($slug)->setCity($city);
        $city->setMainSlug($citySlug);

        $this->slugs[$slug] = $citySlug;

        return $city;
    }

    private function argument(string $name = 'city', string $type = City::class, bool $nullable = false): ArgumentMetadata
    {
        return new ArgumentMetadata($name, $type, false, false, null, $nullable);
    }

    #[Test]
    public function resolvesCityFromRouteAttribute(): void
    {
        $city = $this->registerCity('hamburg');

        $request = Request::create('/hamburg');
        $request->attributes->set('citySlug', 'hamburg');

        self::assertSame([$city], $this->resolver()->resolve($request, $this->argument()));
    }

    #[Test]
    public function resolvesCityFromQueryParameter(): void
    {
        $city = $this->registerCity('berlin');

        $request = Request::create('/api/rides?citySlug=berlin');

        self::assertSame([$city], $this->resolver()->resolve($request, $this->argument()));
    }

    #[Test]
    public function ignoresArgumentsOfOtherTypeOrName(): void
    {
        $this->registerCity('hamburg');
        $request = Request::create('/?citySlug=hamburg');

        self::assertSame([], $this->resolver()->resolve($request, $this->argument('city', \stdClass::class)));
        self::assertSame([], $this->resolver()->resolve($request, $this->argument('town')));
    }

    #[Test]
    public function throwsNotFoundForUnknownSlug(): void
    {
        $request = Request::create('/?citySlug=atlantis');

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('City not found');

        $this->resolver()->resolve($request, $this->argument());
    }

    #[Test]
    public function unknownSlugYieldsNothingForNullableArgument(): void
    {
        $request = Request::create('/?citySlug=atlantis');

        self::assertSame([], $this->resolver()->resolve($request, $this->argument(nullable: true)));
    }

    #[Test]
    public function slugWithoutCityIsNotFound(): void
    {
        $this->slugs['orphan'] = (new CitySlug())->setSlug('orphan');
        $request = Request::create('/?citySlug=orphan');

        $this->expectException(NotFoundHttpException::class);

        $this->resolver()->resolve($request, $this->argument());
    }
}
