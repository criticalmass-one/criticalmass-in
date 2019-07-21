<?php declare(strict_types=1);

namespace Tests\ParamConverter;

use App\Entity\Board;
use App\Repository\BoardRepository;
use App\Request\ParamConverter\BoardParamConverter;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter as ParamConverterConfig;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BoardParamConverterTest extends TestCase
{
    public function testBoardParamConverterSupportsBoard(): void
    {
        $registry = $this->createMock(RegistryInterface::class);

        $paramConverter = new BoardParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:Board',
        ]);

        $this->assertTrue($paramConverter->supports($config));

        $config = new ParamConverterConfig([
            'class' => 'App:SomeTestEntity',
        ]);

        $this->assertFalse($paramConverter->supports($config));
    }

    public function testBoardParamConverterWithEmptyRequest(): void
    {
        $registry = $this->createMock(RegistryInterface::class);

        $paramConverter = new BoardParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:Board',
        ]);

        $request = new Request();

        $this->expectException(NotFoundHttpException::class);

        $paramConverter->apply($request, $config);
    }

    public function testBoardParamConverterWithBoardId(): void
    {
        $board = new Board();

        $boardRepository = $this->createMock(BoardRepository::class);
        $boardRepository
            ->expects($this->once())
            ->method('__call')
            ->withConsecutive($this->equalTo('findOneById'), $this->equalTo(1))
            ->will($this->returnValue($board));

        $registry = $this->createMock(RegistryInterface::class);
        $registry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(Board::class))
            ->will($this->returnValue($boardRepository));

        $paramConverter = new BoardParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:Board',
            'name' => 'board',
        ]);

        $request = new Request(['boardId' => 1]);

        $paramConverter->apply($request, $config);

        $this->assertCount(1, $request->attributes);
        $this->assertEquals($board, $request->attributes->get('board'));
    }

    public function testBoardParamConverterWithBoardSlug(): void
    {
        $board = new Board();

        $boardRepository = $this->createMock(BoardRepository::class);
        $boardRepository
            ->expects($this->once())
            ->method('__call')
            ->withConsecutive($this->equalTo('findOneBySlug'), $this->equalTo('test-slug-board'))
            ->will($this->returnValue($board));

        $registry = $this->createMock(RegistryInterface::class);
        $registry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(Board::class))
            ->will($this->returnValue($boardRepository));

        $paramConverter = new BoardParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:Board',
            'name' => 'board',
        ]);

        $request = new Request(['boardSlug' => 'test-slug-board']);

        $paramConverter->apply($request, $config);

        $this->assertCount(1, $request->attributes);
        $this->assertEquals($board, $request->attributes->get('board'));
    }
}