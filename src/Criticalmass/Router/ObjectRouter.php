<?php declare(strict_types=1);

namespace App\Criticalmass\Router;

use App\Criticalmass\Router\DelegatedRouterManager\DelegatedRouterManagerInterface;
use App\Criticalmass\Router\ParameterResolver\ClassParameterResolver;
use App\Criticalmass\Router\ParameterResolver\PropertyParameterResolver;
use App\EntityInterface\RouteableInterface;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class ObjectRouter extends AbstractObjectRouter implements ObjectRouterInterface
{
    /** @var DelegatedRouterManagerInterface $delegatedRouterManager */
    protected $delegatedRouterManager;

    public function __construct(RouterInterface $router, DelegatedRouterManagerInterface $delegatedRouterManager, Reader $annotationReader, ClassParameterResolver $classParameterResolver, PropertyParameterResolver $propertyParameterResolver)
    {
        $this->delegatedRouterManager = $delegatedRouterManager->setObjectRouter($this);

        parent::__construct($router, $annotationReader, $classParameterResolver, $propertyParameterResolver);
    }

    public function generate(RouteableInterface $routeable, string $routeName = null, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        if (!$routeName) {
            $routeName = $this->getDefaultRouteName($routeable);
        }

        if ($delegatedRouter = $this->delegatedRouterManager->findDelegatedRouter($routeable)) {
            return $delegatedRouter->generate($routeable, $routeName, $parameters, $referenceType);
        }

        if ($routeName) {
            $parameterList = array_merge($this->generateParameterList($routeable, $routeName), $parameters);

            try {
                return $this->router->generate($routeName, $parameterList, $referenceType);
            } catch (InvalidParameterException $exception) {
                $delegatedRouter = $this->delegatedRouterManager->findDelegatedRouter($routeable);

                return $delegatedRouter->generate($routeable, $routeName, $parameters, $referenceType);
            }
        }
    }
}
