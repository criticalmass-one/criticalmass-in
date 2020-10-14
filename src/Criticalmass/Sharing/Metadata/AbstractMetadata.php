<?php declare(strict_types=1);

namespace App\Criticalmass\Sharing\Metadata;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Criticalmass\Sharing\Annotation\Route;
use App\Criticalmass\Sharing\Annotation\RouteParameter;
use App\Criticalmass\Sharing\Annotation\Shorturl;
use App\Criticalmass\Sharing\ShareableInterface\Shareable;
use Caldera\YourlsApiManager\YourlsApiManager;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\RouterInterface;

abstract class AbstractMetadata implements MetadataInterface
{
    /** @var ObjectRouterInterface $router */
    protected $router;

    /** @var Reader $annotationReader */
    protected $annotationReader;

    /** @var YourlsApiManager $yourlsApiManager */
    protected $yourlsApiManager;

    /** @var ManagerRegistry $doctrine */
    protected $doctrine;

    public function __construct(ObjectRouterInterface $router, Reader $annotationReader, YourlsApiManager $yourlsApiManager, ManagerRegistry $doctrine)
    {
        $this->router = $router;
        $this->annotationReader = $annotationReader;
        $this->yourlsApiManager = $yourlsApiManager;
        $this->doctrine = $doctrine;
    }

    protected function generateShareUrl(Shareable $shareable): string
    {
        return $this->router->generate($shareable, null, [], RouterInterface::ABSOLUTE_URL);
    }

    protected function checkShorturl(Shareable $shareable): ?string
    {
        if ($permalinkKeyword = $this->getShorturl($shareable)) {
            return $permalinkKeyword;
        }

        $shorturl = $this->yourlsApiManager->createShorturl($this->generateShareUrl($shareable), $this->getShareTitle($shareable));

        $this->setShorturl($shareable, $shorturl);

        $this->doctrine->getManager()->flush();

        return $shorturl;
    }

    protected function getRouteName(Shareable $shareable): string
    {
        $reflectionClass = new \ReflectionClass($shareable);
        $routeAnnotation = $this->annotationReader->getClassAnnotation($reflectionClass, Route::class);

        return $routeAnnotation->getRoute();
    }

    protected function getRouteParameter(Shareable $shareable): array
    {
        $parameter = [];

        $reflectionClass = new \ReflectionClass($shareable);
        $properties = $reflectionClass->getProperties();

        foreach ($properties as $key => $property) {
            $parameterAnnotation = $this->annotationReader->getPropertyAnnotation($property, RouteParameter::class);

            if ($parameterAnnotation) {
                $getMethodName = sprintf('get%s', ucfirst($property->getName()));

                if (!$reflectionClass->hasMethod($getMethodName)) {
                    continue;
                }

                $value = $shareable->$getMethodName();

                $parameter[$parameterAnnotation->getName()] = $value;
            }
        }

        return $parameter;
    }

    protected function getShorturlPropertyName(Shareable $shareable): ?string
    {
        $reflectionClass = new \ReflectionClass($shareable);
        $properties = $reflectionClass->getProperties();

        foreach ($properties as $key => $property) {
            $permalinkKeywordAnnotation = $this->annotationReader->getPropertyAnnotation($property, Shorturl::class);

            if ($permalinkKeywordAnnotation) {
                return $property->getName();
            }
        }

        return null;
    }
}
