<?php declare(strict_types=1);

namespace App\Criticalmass\Router\DelegatedRouterManager;

use App\Criticalmass\Router\DelegatedRouter\DelegatedRouterInterface;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\EntityInterface\RouteableInterface;

class DelegatedRouterManager implements DelegatedRouterManagerInterface
{
    /** @var array $delegatedRouterList */
    protected $delegatedRouterList = [];

    /** @var ObjectRouterInterface $objectRouter */
    protected $objectRouter;

    public function setObjectRouter(ObjectRouterInterface $objectRouter): DelegatedRouterManagerInterface
    {
        $this->objectRouter = $objectRouter;

        return $this;
    }

    public function addDelegatedRouter(DelegatedRouterInterface $delegatedRouter): DelegatedRouterManagerInterface
    {
        $this->delegatedRouterList[] = $delegatedRouter;

        return $this;
    }

    public function findDelegatedRouter(RouteableInterface $routeable): ?DelegatedRouterInterface
    {
        /** @var DelegatedRouterInterface $delegatedRouter */
        foreach ($this->delegatedRouterList as $delegatedRouter) {
            if ($delegatedRouter->supports($routeable)) {
                return $delegatedRouter->setObjectRouter($this->objectRouter);
            }
        }

        return null;
    }
}