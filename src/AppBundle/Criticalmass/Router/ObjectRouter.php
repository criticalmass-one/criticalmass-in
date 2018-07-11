<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Router;

use AppBundle\Criticalmass\Router\DelegatedRouter\DelegatedRouterInterface;
use AppBundle\EntityInterface\RouteableInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ObjectRouter extends AbstractObjectRouter implements ObjectRouterInterface
{
    /** @var array $delegatedRouterList */
    protected $delegatedRouterList = [];

    public function generate(RouteableInterface $routeable, string $routeName = null, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        if (!$routeName) {
            $routeName = $this->getDefaultRouteName($routeable);
        }

        if ($routeName) {
            $parameterList = array_merge($this->generateParameterList($routeable, $routeName), $parameters);

            return $this->router->generate($routeName, $parameterList, $referenceType);
        } else {
            $delegatedRouter = $this->findDelegatedRouter($routeable);

            return $delegatedRouter->generate($routeable, $routeName, $parameters, $referenceType);
        }
    }

    public function addDelegatedRouter(DelegatedRouterInterface $delegatedRouter): ObjectRouterInterface
    {
        $this->delegatedRouterList[] = $delegatedRouter;

        return $this;
    }

    protected function findDelegatedRouter(RouteableInterface $routeable): DelegatedRouterInterface
    {
        /** @var DelegatedRouterInterface $delegatedRouter */
        foreach ($this->delegatedRouterList as $delegatedRouter) {
            if ($delegatedRouter->supports($routeable)) {
                return $delegatedRouter;
            }
        }

        return null;
    }
}
