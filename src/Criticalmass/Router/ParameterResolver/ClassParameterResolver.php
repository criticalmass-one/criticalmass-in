<?php declare(strict_types=1);

namespace App\Criticalmass\Router\ParameterResolver;

use App\Criticalmass\Router\Annotation\DefaultParameter;
use App\EntityInterface\RouteableInterface;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ClassParameterResolver extends AbstractParameterResolver
{
    /** @var ParameterBagInterface $parameterBag */
    protected $parameterBag;

    public function __construct(Reader $annotationReader, ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;

        parent::__construct($annotationReader);
    }

    public function resolve(RouteableInterface $routeable, string $variableName): ?string
    {
        $reflectionClass = new \ReflectionClass($routeable);

        $classAnnotations = $this->annotationReader->getClassAnnotations($reflectionClass);

        foreach ($classAnnotations as $classAnnotation) {
            if ($classAnnotation instanceof DefaultParameter) {
                if ($classAnnotation->getRouteParameterName() !== $variableName) {
                    continue;
                }

                if ($this->parameterBag->has($classAnnotation->getParameterName())) {
                    return $this->parameterBag->get($classAnnotation->getParameterName());
                }
            }
        }

        return null;
    }
}