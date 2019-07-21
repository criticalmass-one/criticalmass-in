<?php declare(strict_types=1);

namespace Tests\ParamConverter;

use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\Ride;
use App\Repository\CitySlugRepository;
use App\Repository\RideRepository;
use App\Repository\RideSlugRepository;
use App\Request\ParamConverter\RideParamConverter;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter as ParamConverterConfig;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RideParamConverterTest extends TestCase
{
    public function testRideParamConverterSupportsRide(): void
    {
        $registry = $this->createMock(RegistryInterface::class);

        $paramConverter = new RideParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:Ride',
        ]);

        $this->assertTrue($paramConverter->supports($config));

        $config = new ParamConverterConfig([
            'class' => 'App:SomeTestEntity',
        ]);

        $this->assertFalse($paramConverter->supports($config));
    }

    public function testRideParamConverterWithEmptyRequest(): void
    {
        $registry = $this->createMock(RegistryInterface::class);

        $paramConverter = new RideParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:Ride',
        ]);

        $request = new Request();

        $this->expectException(NotFoundHttpException::class);

        $paramConverter->apply($request, $config);
    }

    public function testRideParamConverterWithRideId(): void
    {
        $ride = new Ride();

        $rideRepository = $this->createMock(RideRepository::class);
        $rideRepository
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(123))
            ->will($this->returnValue($ride));

        $registry = $this->createMock(RegistryInterface::class);
        $registry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(Ride::class))
            ->will($this->returnValue($rideRepository));

        $paramConverter = new RideParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:Ride',
            'name' => 'ride',
        ]);

        $request = new Request(['rideId' => 123]);

        $paramConverter->apply($request, $config);

        $this->assertCount(1, $request->attributes);
        $this->assertEquals($ride, $request->attributes->get('ride'));
    }

    public function testRideParamConverterWithRideIdentifier(): void
    {
        $city = new City();
        $ride = new Ride();
        $ride->setCity($city);

        $citySlug = new CitySlug();
        $citySlug->setCity($city);

        $citySlugRepository = $this->createMock(CitySlugRepository::class);
        $citySlugRepository
            ->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('findOneBySlug'), $this->equalTo(['test-city-slug']))
            ->will($this->returnValue($citySlug));

        $rideRepository = $this->createMock(RideRepository::class);
        $rideRepository
            ->expects($this->once())
            ->method('findOneByCityAndSlug')
            ->with($this->equalTo($city), $this->equalTo('test-ride-identifier'))
            ->will($this->returnValue($ride));

        $registry = $this->createMock(RegistryInterface::class);

        $registry
            ->expects($this->any())
            ->method('getRepository')
            ->withConsecutive($this->equalTo(CitySlug::class), $this->equalTo(Ride::class))
            ->willReturnOnConsecutiveCalls($citySlugRepository, $rideRepository);

        $paramConverter = new RideParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:Ride',
            'name' => 'ride',
        ]);

        $request = new Request([
            'citySlug' => 'test-city-slug',
            'rideIdentifier' => 'test-ride-identifier'
        ]);

        $paramConverter->apply($request, $config);

        $this->assertCount(1, $request->attributes);
        $this->assertEquals($ride, $request->attributes->get('ride'));
    }

    public function testRideParamConverterWithRideYearMonthDay(): void
    {
        $city = new City();
        $ride = new Ride();
        $ride->setCity($city);

        $citySlug = new CitySlug();
        $citySlug->setCity($city);

        $citySlugRepository = $this->createMock(CitySlugRepository::class);
        $citySlugRepository
            ->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('findOneBySlug'), $this->equalTo(['test-city-slug']))
            ->will($this->returnValue($citySlug));

        $rideRepository = $this->createMock(RideRepository::class);
        $rideRepository
            ->expects($this->once())
            ->method('findCityRideByDate')
            ->with($this->equalTo($city), $this->equalTo(new \DateTime('2011-06-24 00:00:00')))
            ->will($this->returnValue($ride));

        $registry = $this->createMock(RegistryInterface::class);

        $registry
            ->expects($this->any())
            ->method('getRepository')
            ->withConsecutive($this->equalTo(CitySlug::class), $this->equalTo(Ride::class))
            ->willReturnOnConsecutiveCalls($citySlugRepository, $rideRepository);

        $paramConverter = new RideParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:Ride',
            'name' => 'ride',
        ]);

        $request = new Request([
            'citySlug' => 'test-city-slug',
            'rideIdentifier' => '2011-06-24'
        ]);

        $paramConverter->apply($request, $config);

        $this->assertCount(1, $request->attributes);
        $this->assertEquals($ride, $request->attributes->get('ride'));
    }

    public function testRideParamConverterWithRideYearMonth(): void
    {
        $city = new City();
        $ride = new Ride();
        $ride->setCity($city);

        $citySlug = new CitySlug();
        $citySlug->setCity($city);

        $citySlugRepository = $this->createMock(CitySlugRepository::class);
        $citySlugRepository
            ->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('findOneBySlug'), $this->equalTo(['test-city-slug']))
            ->will($this->returnValue($citySlug));

        $rideRepository = $this->createMock(RideRepository::class);
        $rideRepository
            ->expects($this->once())
            ->method('findByCityAndMonth')
            ->with($this->equalTo($city), $this->equalTo(new \DateTime('2011-06-01 00:00:00')))
            ->will($this->returnValue([$ride]));

        $registry = $this->createMock(RegistryInterface::class);

        $registry
            ->expects($this->any())
            ->method('getRepository')
            ->withConsecutive($this->equalTo(CitySlug::class), $this->equalTo(Ride::class))
            ->willReturnOnConsecutiveCalls($citySlugRepository, $rideRepository);

        $paramConverter = new RideParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:Ride',
            'name' => 'ride',
        ]);

        $request = new Request([
            'citySlug' => 'test-city-slug',
            'rideIdentifier' => '2011-06'
        ]);

        $paramConverter->apply($request, $config);

        $this->assertCount(1, $request->attributes);
        $this->assertEquals($ride, $request->attributes->get('ride'));
    }
}