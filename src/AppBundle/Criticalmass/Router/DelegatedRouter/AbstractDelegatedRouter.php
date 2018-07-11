<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Router\DelegatedRouter;

use AppBundle\Criticalmass\Router\AbstractObjectRouter;
use AppBundle\EntityInterface\RouteableInterface;

abstract class AbstractDelegatedRouter extends AbstractObjectRouter implements DelegatedRouterInterface
{
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

        $fqcn = sprintf('AppBundle\\Entity\\%s', $entityClassName);

        return $fqcn;
    }
}
