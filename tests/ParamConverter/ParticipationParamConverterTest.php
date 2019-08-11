<?php declare(strict_types=1);

namespace Tests\ParamConverter;

use App\Entity\Participation;
use App\Repository\ParticipationRepository;
use App\Request\ParamConverter\ParticipationParamConverter;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter as ParamConverterConfig;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ParticipationParamConverterTest extends TestCase
{
    public function testParticipationParamConverterSupportsParticipation(): void
    {
        $registry = $this->createMock(RegistryInterface::class);

        $paramConverter = new ParticipationParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:Participation',
        ]);

        $this->assertTrue($paramConverter->supports($config));

        $config = new ParamConverterConfig([
            'class' => 'App:SomeTestEntity',
        ]);

        $this->assertFalse($paramConverter->supports($config));
    }

    public function testParticipationParamConverterWithEmptyRequest(): void
    {
        $registry = $this->createMock(RegistryInterface::class);

        $paramConverter = new ParticipationParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:Participation',
        ]);

        $request = new Request();

        $this->expectException(NotFoundHttpException::class);

        $paramConverter->apply($request, $config);
    }

    public function testParticipationParamConverterWithParticipationId(): void
    {
        $participation = new Participation();

        $participationRepository = $this->createMock(ParticipationRepository::class);
        $participationRepository
            ->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('findOneById'), $this->equalTo([33]))
            ->will($this->returnValue($participation));

        $registry = $this->createMock(RegistryInterface::class);
        $registry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(Participation::class))
            ->will($this->returnValue($participationRepository));

        $paramConverter = new ParticipationParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:Participation',
            'name' => 'participation',
        ]);

        $request = new Request(['participationId' => 33]);

        $paramConverter->apply($request, $config);

        $this->assertCount(1, $request->attributes);
        $this->assertEquals($participation, $request->attributes->get('participation'));
    }
}
