<?php declare(strict_types=1);

namespace App\Criticalmass\Router\DelegatedRouterManager;

use App\Criticalmass\Router\DelegatedRouter\DelegatedRouterInterface;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\EntityInterface\RouteableInterface;

class DelegatedRouterManager implements DelegatedRouterManagerInterface
{
    protected array $delegatedRouterList = [];

    protected ?ObjectRouterInterface $objectRouter = null;

    public function setObjectRouter(ObjectRouterInterface $objectRouter): DelegatedRouterManagerInterface
    {
        $this->objectRouter = $objectRouter;

        foreach ($this->delegatedRouterList as $delegatedRouter) {
            $delegatedRouter->setObjectRouter($objectRouter);
        }

        return $this;
    }

    public function addDelegatedRouter(DelegatedRouterInterface $delegatedRouter): DelegatedRouterManagerInterface
    {
        $this->delegatedRouterList[] = $delegatedRouter;

        if ($this->objectRouter) {
            $delegatedRouter->setObjectRouter($this->objectRouter);
        }

        return $this;
    }

    public function findDelegatedRouter(RouteableInterface $routeable): ?DelegatedRouterInterface
    {
        foreach ($this->delegatedRouterList as $delegatedRouter) {
            if ($delegatedRouter->supports($routeable)) {
                return $delegatedRouter;
            }
        }

        return null;
    }
}