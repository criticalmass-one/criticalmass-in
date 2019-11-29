<?php declare(strict_types=1);

namespace Tests\ParamConverter;

use App\Entity\CityCycle;
use App\Repository\CityCycleRepository;
use App\Request\ParamConverter\CityCycleParamConverter;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter as ParamConverterConfig;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CityCycleParamConverterTest extends TestCase
{
    public function testCityCycleParamConverterSupportsCityCycle(): void
    {
        $registry = $this->createMock(RegistryInterface::class);

        $paramConverter = new CityCycleParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:CityCycle',
        ]);

        $this->assertTrue($paramConverter->supports($config));

        $config = new ParamConverterConfig([
            'class' => 'App:SomeTestEntity',
        ]);

        $this->assertFalse($paramConverter->supports($config));
    }

    public function testCityCycleParamConverterWithEmptyRequest(): void
    {
        $registry = $this->createMock(RegistryInterface::class);

        $paramConverter = new CityCycleParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:CityCycle',
        ]);

        $request = new Request();

        $this->expectException(NotFoundHttpException::class);

        $paramConverter->apply($request, $config);
    }

    public function testCityCycleParamConverterWithCityCycleId(): void
    {
        $cityCycle = new CityCycle();

        $cityCycleRepository = $this->createMock(CityCycleRepository::class);
        $cityCycleRepository
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(3))
            ->will($this->returnValue($cityCycle));

        $registry = $this->createMock(RegistryInterface::class);
        $registry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(CityCycle::class))
            ->will($this->returnValue($cityCycleRepository));

        $paramConverter = new CityCycleParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:CityCycle',
            'name' => 'cityCycle',
        ]);

        $request = new Request(['cityCycleId' => 3]);

        $paramConverter->apply($request, $config);

        $this->assertCount(1, $request->attributes);
        $this->assertEquals($cityCycle, $request->attributes->get('cityCycle'));
    }
}