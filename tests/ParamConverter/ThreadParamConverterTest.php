<?php declare(strict_types=1);

namespace Tests\ParamConverter;

use App\Entity\Thread;
use App\Repository\ThreadRepository;
use App\Request\ParamConverter\ThreadParamConverter;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter as ParamConverterConfig;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ThreadParamConverterTest extends TestCase
{
    public function testThreadParamConverterSupportsThread(): void
    {
        $registry = $this->createMock(RegistryInterface::class);

        $paramConverter = new ThreadParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:Thread',
        ]);

        $this->assertTrue($paramConverter->supports($config));

        $config = new ParamConverterConfig([
            'class' => 'App:SomeTestEntity',
        ]);

        $this->assertFalse($paramConverter->supports($config));
    }

    public function testThreadParamConverterWithEmptyRequest(): void
    {
        $registry = $this->createMock(RegistryInterface::class);

        $paramConverter = new ThreadParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:Thread',
        ]);

        $request = new Request();

        $this->expectException(NotFoundHttpException::class);

        $paramConverter->apply($request, $config);
    }

    public function testThreadParamConverterWithThreadId(): void
    {
        $thread = new Thread();

        $threadRepository = $this->createMock(ThreadRepository::class);
        $threadRepository
            ->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('findOneById'), $this->equalTo([51]))
            ->will($this->returnValue($thread));

        $registry = $this->createMock(RegistryInterface::class);
        $registry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(Thread::class))
            ->will($this->returnValue($threadRepository));

        $paramConverter = new ThreadParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:Thread',
            'name' => 'thread',
        ]);

        $request = new Request(['threadId' => 51]);

        $paramConverter->apply($request, $config);

        $this->assertCount(1, $request->attributes);
        $this->assertEquals($thread, $request->attributes->get('thread'));
    }
}
