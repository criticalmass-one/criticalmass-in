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
        $fqcn = static::getEntityFqcn();

        return $routeable instanceof $fqcn;
    }

    abstract protected static function getEntityFqcn(): string;
}
