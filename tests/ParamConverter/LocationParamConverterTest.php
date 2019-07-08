<?php declare(strict_types=1);

namespace Tests\ParamConverter;

use App\Entity\Location;
use App\Repository\LocationRepository;
use App\Request\ParamConverter\LocationParamConverter;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter as ParamConverterConfig;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LocationParamConverterTest extends TestCase
{
    public function testLocationParamConverterSupportsLocaction(): void
    {
        $registry = $this->createMock(RegistryInterface::class);

        $paramConverter = new LocationParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:Location',
        ]);

        $this->assertTrue($paramConverter->supports($config));

        $config = new ParamConverterConfig([
            'class' => 'App:SomeTestEntity',
        ]);

        $this->assertFalse($paramConverter->supports($config));
    }

    public function testLocationParamConverterWithEmptyRequest(): void
    {
        $registry = $this->createMock(RegistryInterface::class);

        $paramConverter = new LocationParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:Location',
        ]);

        $request = new Request();

        $this->expectException(NotFoundHttpException::class);

        $paramConverter->apply($request, $config);
    }

    public function testLocationParamConverterWithLocationId(): void
    {
        $location = new Location();

        $locationRepository = $this->createMock(LocationRepository::class);
        $locationRepository
            ->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('findOneById'), $this->equalTo([23]))
            ->will($this->returnValue($location));

        $registry = $this->createMock(RegistryInterface::class);
        $registry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(Location::class))
            ->will($this->returnValue($locationRepository));

        $paramConverter = new LocationParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:Location',
            'name' => 'location',
        ]);

        $request = new Request(['locationId' => 23]);

        $paramConverter->apply($request, $config);

        $this->assertCount(1, $request->attributes);
        $this->assertEquals($location, $request->attributes->get('location'));
    }

    public function testLocationParamConverterWithLocationSlug(): void
    {
        $location = new Location();

        $locationRepository = $this->createMock(LocationRepository::class);
        $locationRepository
            ->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('findOneBySlug'), $this->equalTo(['test-location-slug']))
            ->will($this->returnValue($location));

        $registry = $this->createMock(RegistryInterface::class);

        $registry
            ->method('getRepository')
            ->with($this->equalTo(Location::class))
            ->will($this->returnValue($locationRepository));

        $paramConverter = new LocationParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:Location',
            'name' => 'location',
        ]);

        $request = new Request(['locationSlug' => 'test-location-slug']);

        $paramConverter->apply($request, $config);

        $this->assertCount(1, $request->attributes);
        $this->assertEquals($location, $request->attributes->get('location'));
    }
}