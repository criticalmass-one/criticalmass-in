<?php declare(strict_types=1);

namespace App\Criticalmass\Router\DelegatedRouter;

use App\Criticalmass\Router\AbstractRouter;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\EntityInterface\RouteableInterface;

abstract class AbstractDelegatedRouter extends AbstractRouter implements DelegatedRouterInterface
{

    public function setObjectRouter(ObjectRouterInterface $objectRouter): DelegatedRouterInterface
    {
        $this->objectRouter = $objectRouter;

        return $this;
    }

    public function supports(RouteableInterface $routeable): bool
    {
        $fqcn = $this->getFqcn();

        return $routeable instanceof $fqcn;
    }

    protected function getFqcn(): string
    {
        $routerClassname = get_class($this);

        preg_match('/(.*)\\\([A-Za-z].*)Router/', $routerClassname, $matches);

        $entityClassName = array_pop($matches);

        $fqcn = sprintf('App\\Entity\\%s', $entityClassName);

        return $fqcn;
    }
}
