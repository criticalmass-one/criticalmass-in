<?php declare(strict_types=1);

namespace Tests\ValueResolver;

use App\Entity\Thread;
use App\EntityInterface\PostableInterface;
use App\Repository\ThreadRepository;
use App\ValueResolver\ThreadValueResolver;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ThreadValueResolverTest extends TestCase
{
    private Thread $thread;
    private ThreadValueResolver $resolver;

    protected function setUp(): void
    {
        $this->thread = (new Thread())->setSlug('my-thread');

        $repository = $this->createMock(ThreadRepository::class);
        $repository->method('__call')->willReturnCallback(
            fn (string $method, array $arguments): ?Thread => 'findOneBySlug' === $method && 'my-thread' === $arguments[0] ? $this->thread : null
        );

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getRepository')->with(Thread::class)->willReturn($repository);

        $this->resolver = new ThreadValueResolver($registry);
    }

    #[Test]
    public function resolvesThreadFromRouteAttribute(): void
    {
        $request = Request::create('/boards/x/thread/my-thread');
        $request->attributes->set('threadSlug', 'my-thread');

        $argument = new ArgumentMetadata('thread', Thread::class, false, false, null);

        self::assertSame([$this->thread], $this->resolver->resolve($request, $argument));
    }

    #[Test]
    public function resolvesPostableInterfaceArgumentsToo(): void
    {
        $request = Request::create('/?threadSlug=my-thread');

        $argument = new ArgumentMetadata('postable', PostableInterface::class, false, false, null);

        self::assertSame([$this->thread], $this->resolver->resolve($request, $argument));
    }

    #[Test]
    public function ignoresUnrelatedArgumentTypes(): void
    {
        $request = Request::create('/?threadSlug=my-thread');

        $argument = new ArgumentMetadata('thread', \stdClass::class, false, false, null);

        self::assertSame([], $this->resolver->resolve($request, $argument));
    }

    #[Test]
    public function unknownSlugThrowsNotFound(): void
    {
        $request = Request::create('/?threadSlug=nope');

        $this->expectException(NotFoundHttpException::class);

        $this->resolver->resolve($request, new ArgumentMetadata('thread', Thread::class, false, false, null));
    }

    #[Test]
    public function unknownSlugYieldsNullForNullableArgument(): void
    {
        $request = Request::create('/?threadSlug=nope');

        $argument = new ArgumentMetadata('thread', Thread::class, false, false, null, true);

        self::assertSame([null], $this->resolver->resolve($request, $argument));
    }
}
