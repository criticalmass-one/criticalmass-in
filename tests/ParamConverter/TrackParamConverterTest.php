<?php declare(strict_types=1);

namespace Tests\ParamConverter;

use App\Entity\Track;
use App\Repository\TrackRepository;
use App\Request\ParamConverter\TrackParamConverter;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter as ParamConverterConfig;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TrackParamConverterTest extends TestCase
{
    public function testTrackParamConverterSupportsLocaction(): void
    {
        $registry = $this->createMock(RegistryInterface::class);

        $paramConverter = new TrackParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:Track',
        ]);

        $this->assertTrue($paramConverter->supports($config));

        $config = new ParamConverterConfig([
            'class' => 'App:SomeTestEntity',
        ]);

        $this->assertFalse($paramConverter->supports($config));
    }

    public function testTrackParamConverterWithEmptyRequest(): void
    {
        $registry = $this->createMock(RegistryInterface::class);

        $paramConverter = new TrackParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:Track',
        ]);

        $request = new Request();

        $this->expectException(NotFoundHttpException::class);

        $paramConverter->apply($request, $config);
    }

    public function testTrackParamConverterWithTrackId(): void
    {
        $track = new Track();

        $trackRepository = $this->createMock(TrackRepository::class);
        $trackRepository
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(51))
            ->will($this->returnValue($track));

        $registry = $this->createMock(RegistryInterface::class);
        $registry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(Track::class))
            ->will($this->returnValue($trackRepository));

        $paramConverter = new TrackParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:Track',
            'name' => 'track',
        ]);

        $request = new Request(['trackId' => 51]);

        $paramConverter->apply($request, $config);

        $this->assertCount(1, $request->attributes);
        $this->assertEquals($track, $request->attributes->get('track'));
    }
}
