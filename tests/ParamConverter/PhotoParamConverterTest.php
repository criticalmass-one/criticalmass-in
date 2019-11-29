<?php declare(strict_types=1);

namespace Tests\ParamConverter;

use App\Entity\Photo;
use App\Repository\PhotoRepository;
use App\Request\ParamConverter\PhotoParamConverter;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter as ParamConverterConfig;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PhotoParamConverterTest extends TestCase
{
    public function testPhotoParamConverterSupportsPhoto(): void
    {
        $registry = $this->createMock(RegistryInterface::class);

        $paramConverter = new PhotoParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:Photo',
        ]);

        $this->assertTrue($paramConverter->supports($config));

        $config = new ParamConverterConfig([
            'class' => 'App:SomeTestEntity',
        ]);

        $this->assertFalse($paramConverter->supports($config));
    }

    public function testPhotoParamConverterWithEmptyRequest(): void
    {
        $registry = $this->createMock(RegistryInterface::class);

        $paramConverter = new PhotoParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:Photo',
        ]);

        $request = new Request();

        $this->expectException(NotFoundHttpException::class);

        $paramConverter->apply($request, $config);
    }

    public function testPhotoParamConverterWithPhotoId(): void
    {
        $photo = new Photo();

        $photoRepository = $this->createMock(PhotoRepository::class);
        $photoRepository
            ->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('findOneById'), $this->equalTo([51]))
            ->will($this->returnValue($photo));

        $registry = $this->createMock(RegistryInterface::class);
        $registry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(Photo::class))
            ->will($this->returnValue($photoRepository));

        $paramConverter = new PhotoParamConverter($registry);

        $config = new ParamConverterConfig([
            'class' => 'App:Photo',
            'name' => 'photo',
        ]);

        $request = new Request(['photoId' => 51]);

        $paramConverter->apply($request, $config);

        $this->assertCount(1, $request->attributes);
        $this->assertEquals($photo, $request->attributes->get('photo'));
    }
}
