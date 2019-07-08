<?php declare(strict_types=1);

namespace Tests\ParamConverter;

use App\Entity\City;
use App\Entity\CitySlug;
use App\Repository\CityRepository;
use App\Repository\CitySlugRepository;
use App\Request\ParamConverter\CityParamConverter;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter as ParamConverterConfig;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CityParamConverterTest extends TestCase
{
    public function testCityParamConverterSupportsCity(): void
    {
        $registry = $this->createMock(RegistryInterface::class);

        $paramConverter = new CityParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:City',
        ]);

        $this->assertTrue($paramConverter->supports($config));

        $config = new ParamConverterConfig([
            'class' => 'App:SomeTestEntity',
        ]);

        $this->assertFalse($paramConverter->supports($config));
    }

    public function testCityParamConverterWithEmptyRequest(): void
    {
        $registry = $this->createMock(RegistryInterface::class);

        $paramConverter = new CityParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:City',
        ]);

        $request = new Request();

        $this->expectException(NotFoundHttpException::class);

        $paramConverter->apply($request, $config);
    }

    public function testCityParamConverterWithCityId(): void
    {
        $city = new City();

        $cityRepository = $this->createMock(CityRepository::class);
        $cityRepository
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(167))
            ->will($this->returnValue($city));

        $registry = $this->createMock(RegistryInterface::class);
        $registry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(City::class))
            ->will($this->returnValue($cityRepository));

        $paramConverter = new CityParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:City',
            'name' => 'city',
        ]);

        $request = new Request(['cityId' => 167]);

        $paramConverter->apply($request, $config);

        $this->assertCount(1, $request->attributes);
        $this->assertEquals($city, $request->attributes->get('city'));
    }

    public function testCityParamConverterWithCitySlug(): void
    {
        $citySlug = new CitySlug();
        $city = new City();

        $city->addSlug($citySlug);
        $citySlug
            ->setCity($city)
            ->setSlug('test-slug-city');

        $cityRepository = $this->createMock(CityRepository::class);
        $cityRepository
            ->expects($this->never())
            ->method('find');

        $citySlugRepository = $this->createMock(CitySlugRepository::class);
        $citySlugRepository
            ->expects($this->once())
            ->method('__call')
            ->withConsecutive($this->equalTo('findOneBySlug'), $this->equalTo('test-slug-city'))
            ->will($this->returnValue($citySlug));

        $registry = $this->createMock(RegistryInterface::class);

        $registry
            ->method('getRepository')
            ->with($this->equalTo(CitySlug::class))
            ->will($this->returnValue($citySlugRepository));

        $paramConverter = new CityParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:City',
            'name' => 'city',
        ]);

        $request = new Request(['citySlug' => 'test-slug-city']);

        $paramConverter->apply($request, $config);

        $this->assertCount(1, $request->attributes);
        $this->assertEquals($city, $request->attributes->get('city'));
    }
}