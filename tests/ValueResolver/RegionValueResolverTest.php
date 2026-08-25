<?php declare(strict_types=1);

namespace Tests\ValueResolver;

use App\Entity\Region;
use App\Repository\RegionRepository;
use App\ValueResolver\RegionValueResolver;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class RegionValueResolverTest extends TestCase
{
    private Region $region;
    private RegionValueResolver $resolver;

    protected function setUp(): void
    {
        $this->region = (new Region())->setName('Europe')->setSlug('europe');

        $repository = $this->createMock(RegionRepository::class);
        $repository->method('findOneBy')->willReturnCallback(
            fn (array $criteria): ?Region => 'europe' === ($criteria['slug'] ?? null) ? $this->region : null
        );

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getRepository')->with(Region::class)->willReturn($repository);

        $this->resolver = new RegionValueResolver($registry);
    }

    private function argument(string $name = 'region', string $type = Region::class): ArgumentMetadata
    {
        return new ArgumentMetadata($name, $type, false, false, null);
    }

    #[Test]
    public function resolvesRegionFromQueryParameter(): void
    {
        $request = Request::create('/api/city?regionSlug=europe');

        self::assertSame([$this->region], $this->resolver->resolve($request, $this->argument()));
    }

    #[Test]
    public function onlyTheQueryStringIsConsulted(): void
    {
        $request = Request::create('/world/europe');
        $request->attributes->set('regionSlug', 'europe');

        self::assertSame([], $this->resolver->resolve($request, $this->argument()));
    }

    #[Test]
    public function emptySlugYieldsNothing(): void
    {
        $request = Request::create('/?regionSlug=');

        self::assertSame([], $this->resolver->resolve($request, $this->argument()));
    }

    #[Test]
    public function ignoresArgumentsOfOtherTypeOrName(): void
    {
        $request = Request::create('/?regionSlug=europe');

        self::assertSame([], $this->resolver->resolve($request, $this->argument('area')));
        self::assertSame([], $this->resolver->resolve($request, $this->argument('region', \stdClass::class)));
    }

    #[Test]
    public function unknownSlugThrowsNotFoundWithSlugInMessage(): void
    {
        $request = Request::create('/?regionSlug=narnia');

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Region with slug "narnia" not found.');

        $this->resolver->resolve($request, $this->argument());
    }
}
